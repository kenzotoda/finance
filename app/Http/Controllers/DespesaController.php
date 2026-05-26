<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDespesaRequest;
use App\Http\Requests\UpdateDespesaRequest;
use App\Models\Cartao;
use App\Models\Despesa;
use App\Models\FaturaCartao;
use App\Services\FaturaImport\FaturaImportService;
use App\Services\FaturaImport\FaturaStorageService;
use App\Services\ReplicarDespesasFixasService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DespesaController extends Controller
{
    public function __construct(
        private readonly ReplicarDespesasFixasService $replicarDespesasFixasService,
        private readonly FaturaImportService $faturaImportService,
        private readonly FaturaStorageService $faturaStorageService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $this->replicarDespesasFixasService->execute($user);

        $cartoes = $user->cartoes()->withCount('faturas')->orderBy('nome')->get();
        $selectedCartaoId = (int) request('cartao_id', 0);
        if ($selectedCartaoId === 0 || ! $cartoes->contains('id', $selectedCartaoId)) {
            $selectedCartaoId = (int) ($cartoes->first()?->id ?? 0);
        }

        $selectedCompetencia = (string) request('competencia', '');
        $competenciaFiltro = null;
        if ($selectedCompetencia !== '') {
            try {
                $competenciaFiltro = Carbon::createFromFormat('Y-m', $selectedCompetencia)->startOfMonth();
            } catch (\Throwable) {
                $competenciaFiltro = null;
            }
        }

        $competenciasDisponiveis = $selectedCartaoId > 0
            ? $user->faturasCartao()
                ->where('cartao_id', $selectedCartaoId)
                ->orderByDesc('competencia')
                ->get()
                ->map(fn (FaturaCartao $fatura) => [
                    'value' => $fatura->competencia->format('Y-m'),
                    'label' => $fatura->competencia->format('m/Y'),
                ])
                ->unique('value')
                ->values()
            : collect();

        $faturas = $user->faturasCartao()
            ->with('cartao')
            ->withCount('despesas')
            ->withSum('despesas', 'valor')
            ->when($selectedCartaoId > 0, fn ($query) => $query->where('cartao_id', $selectedCartaoId))
            ->when($competenciaFiltro instanceof Carbon, fn ($query) => $query->whereDate('competencia', $competenciaFiltro->toDateString()))
            ->orderByDesc('competencia')
            ->paginate(12)
            ->withQueryString();

        $faturaPreviewToken = session('fatura_preview_token');
        $faturaPreview = is_string($faturaPreviewToken)
            ? Cache::get($faturaPreviewToken)
            : null;

        return view('despesas.index', compact(
            'faturaPreview',
            'cartoes',
            'faturas',
            'selectedCartaoId',
            'selectedCompetencia',
            'competenciasDisponiveis',
        ));
    }

    public function create(): View
    {
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();
        $permitirParcelamento = true;

        return view('despesas.create', compact('categorias', 'permitirParcelamento'));
    }

    public function store(StoreDespesaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $quantidadeParcelas = (int) $data['quantidade_parcelas'];
        unset($data['quantidade_parcelas']);

        $data['tipo'] = 'variavel';
        $data['origem'] = 'manual';
        $data['despesa_fixa_id'] = null;
        $data['imposto_id'] = null;
        $data['compra_parcelada_id'] = null;
        $data['parcela_atual'] = null;
        $data['total_parcelas'] = null;
        $data['fatura_arquivo'] = null;
        $data['fatura_hash'] = null;
        $data['hash_lancamento'] = null;

        $dataBase = Carbon::parse($data['data']);
        $grupoParceladoId = $quantidadeParcelas > 1 ? (string) Str::uuid() : null;
        $valoresParcelas = $this->calcularValoresParcelas((float) $data['valor'], $quantidadeParcelas);

        for ($parcela = 1; $parcela <= $quantidadeParcelas; $parcela++) {
            $despesaData = $data;
            $despesaData['data'] = $dataBase->copy()->addMonthsNoOverflow($parcela - 1)->toDateString();
            $despesaData['valor'] = $valoresParcelas[$parcela - 1];

            if ($grupoParceladoId) {
                $despesaData['compra_parcelada_id'] = $grupoParceladoId;
                $despesaData['parcela_atual'] = $parcela;
                $despesaData['total_parcelas'] = $quantidadeParcelas;
            }

            Auth::user()->despesas()->create($despesaData);
        }

        return to_route('despesas.index')
            ->with('success', $quantidadeParcelas > 1
                ? "Despesa parcelada em {$quantidadeParcelas}x criada com sucesso."
                : 'Despesa cadastrada com sucesso.');
    }

    public function show(Despesa $despesa): RedirectResponse
    {
        $this->garantirUsuario($despesa);
        $this->garantirDespesaAvulsa($despesa);

        return to_route('despesas.edit', $despesa);
    }

    public function edit(Despesa $despesa): View
    {
        $this->garantirUsuario($despesa);
        $this->garantirDespesaAvulsa($despesa);

        $categorias = Auth::user()->categorias()->orderBy('nome')->get();
        $permitirParcelamento = false;

        return view('despesas.edit', compact('despesa', 'categorias', 'permitirParcelamento'));
    }

    public function update(UpdateDespesaRequest $request, Despesa $despesa): RedirectResponse
    {
        $this->garantirUsuario($despesa);
        $this->garantirDespesaAvulsa($despesa);
        $data = $request->validated();
        $data['tipo'] = 'variavel';
        $data['origem'] = 'manual';
        $data['despesa_fixa_id'] = null;
        $data['imposto_id'] = null;
        $data['fatura_arquivo'] = null;
        $data['fatura_hash'] = null;
        $data['hash_lancamento'] = null;

        $despesa->update($data);

        return to_route('despesas.index')->with('success', 'Despesa atualizada com sucesso.');
    }

    public function destroy(Despesa $despesa): RedirectResponse
    {
        $this->garantirUsuario($despesa);
        $this->garantirDespesaAvulsa($despesa);

        $despesa->delete();

        return to_route('despesas.index')->with('success', 'Despesa removida com sucesso.');
    }

    public function importarFatura(): RedirectResponse
    {
        $data = request()->validate([
            'cartao_id' => ['required', 'integer'],
            'competencia' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'],
            'fatura' => ['required', 'file', 'mimes:csv,txt,ofx', 'max:10240'],
        ], [
            'competencia.regex' => 'Informe a competencia no formato MM/AAAA (ex.: 05/2026).',
        ]);

        $user = Auth::user();
        $cartao = $user->cartoes()->findOrFail((int) $data['cartao_id']);
        $competencia = Carbon::createFromFormat('m/Y', (string) $data['competencia'])->startOfMonth();

        $faturaExistente = $user->faturasCartao()
            ->where('cartao_id', $cartao->id)
            ->whereDate('competencia', $competencia->toDateString())
            ->exists();

        if ($faturaExistente) {
            return to_route('despesas.index', ['cartao_id' => $cartao->id])
                ->with('error', 'Ja existe fatura importada para este cartao na competencia informada.');
        }

        try {
            $arquivoPath = $this->faturaStorageService->storePreview($user, $data['fatura']);
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            report($exception);

            return to_route('despesas.index', ['cartao_id' => $cartao->id])
                ->with('error', 'Falha de conexao com o Supabase S3. Em ambiente local, defina SUPABASE_VERIFY_SSL=false no .env.');
        } catch (\Throwable $exception) {
            report($exception);

            return to_route('despesas.index', ['cartao_id' => $cartao->id])
                ->with('error', 'Nao foi possivel enviar a fatura para o Supabase S3. Verifique as credenciais S3 e se o bucket fatura-dev existe.');
        }

        $preview = $this->faturaImportService->analyze($user, $data['fatura'], $cartao, $competencia);
        $preview['arquivo_path'] = $arquivoPath;
        $previewToken = 'fatura_preview_'.(string) Str::uuid();
        Cache::put($previewToken, $preview, now()->addMinutes(30));
        session(['fatura_preview_token' => $previewToken]);

        return to_route('despesas.index', ['cartao_id' => $cartao->id])->with(
            'success',
            "Previa gerada: {$preview['prontas_importacao']} pronto(s) para importar e {$preview['ignoradas']} ignorado(s)."
        );
    }

    public function confirmarImportacaoFatura(): RedirectResponse
    {
        $user = Auth::user();
        $previewToken = session('fatura_preview_token');
        $preview = is_string($previewToken) ? Cache::get($previewToken) : null;

        if (! is_array($preview) || ! isset($preview['linhas']) || ! is_array($preview['linhas'])) {
            return to_route('despesas.index')->with('error', 'Nenhuma previa de fatura disponivel para confirmar.');
        }

        $faturaExistente = $user->faturasCartao()
            ->where('cartao_id', (int) ($preview['cartao_id'] ?? 0))
            ->whereDate('competencia', (string) ($preview['competencia'] ?? ''))
            ->exists();

        if ($faturaExistente) {
            return to_route('despesas.index', ['cartao_id' => (int) ($preview['cartao_id'] ?? 0)])
                ->with('error', 'Esta fatura ja foi importada para este cartao/competencia.');
        }

        $resultado = $this->faturaImportService->importPreview($user, $preview);
        if (is_string($previewToken)) {
            Cache::forget($previewToken);
        }
        session()->forget('fatura_preview_token');

        return to_route('despesas.index', ['cartao_id' => (int) ($preview['cartao_id'] ?? 0)])->with(
            'success',
            "Importacao concluida: {$resultado['importadas']} lancamento(s) importado(s) e {$resultado['ignoradas']} ignorado(s)."
        );
    }

    public function cancelarImportacaoFatura(): RedirectResponse
    {
        $cartaoId = $this->descartarPreviewDaSessao();

        return to_route('despesas.index', array_filter([
            'cartao_id' => $cartaoId > 0 ? $cartaoId : null,
        ]))->with('success', 'Previa de importacao cancelada.');
    }

    public function storeCartao(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:80'],
            'bandeira' => ['nullable', 'string', 'max:30'],
        ]);

        Auth::user()->cartoes()->create([
            'nome' => $data['nome'],
            'bandeira' => $data['bandeira'] ?? null,
            'ativo' => true,
        ]);

        return to_route('despesas.index')->with('success', 'Cartao cadastrado com sucesso.');
    }

    public function destroyCartao(Cartao $cartao): RedirectResponse
    {
        abort_unless($cartao->user_id === Auth::id(), 403);

        $faturas = $cartao->faturas()->get();
        $arquivoPaths = $faturas
            ->map(fn (FaturaCartao $fatura) => (string) ($fatura->arquivo_path ?? ''))
            ->filter()
            ->values()
            ->all();

        DB::transaction(function () use ($cartao, $faturas): void {
            foreach ($faturas as $fatura) {
                $fatura->despesas()->delete();
            }

            Auth::user()->despesas()->where('cartao_id', $cartao->id)->delete();
            $cartao->faturas()->delete();
            $cartao->delete();
        });

        if ($this->excluirArquivosFatura($arquivoPaths)) {
            return to_route('despesas.index')->with(
                'error',
                'Cartao removido, mas nao foi possivel excluir um ou mais arquivos importados.'
            );
        }

        return to_route('despesas.index')->with('success', 'Cartao removido com sucesso.');
    }

    public function showFatura(FaturaCartao $faturaCartao): View
    {
        abort_unless($faturaCartao->user_id === Auth::id(), 403);

        $faturaCartao->load(['cartao', 'despesas']);

        return view('despesas.fatura', [
            'fatura' => $faturaCartao,
            'despesas' => $faturaCartao->despesas()->orderByDesc('data')->paginate(50),
        ]);
    }

    public function destroyFatura(FaturaCartao $faturaCartao): RedirectResponse
    {
        abort_unless($faturaCartao->user_id === Auth::id(), 403);

        $cartaoId = $faturaCartao->cartao_id;
        $arquivoPath = (string) ($faturaCartao->arquivo_path ?? '');

        DB::transaction(function () use ($faturaCartao): void {
            $faturaCartao->despesas()->delete();
            $faturaCartao->delete();
        });

        if ($arquivoPath !== '' && $this->excluirArquivosFatura([$arquivoPath])) {
            return to_route('despesas.index', ['cartao_id' => $cartaoId])->with(
                'error',
                'Fatura removida, mas nao foi possivel excluir o arquivo importado.'
            );
        }

        return to_route('despesas.index', ['cartao_id' => $cartaoId])->with(
            'success',
            'Fatura removida com sucesso.'
        );
    }

    private function excluirArquivosFatura(array $paths): bool
    {
        $falhou = false;

        foreach ($paths as $path) {
            if ($path === '') {
                continue;
            }

            try {
                $this->faturaStorageService->delete($path);
            } catch (\Throwable $exception) {
                report($exception);
                $falhou = true;
            }
        }

        return $falhou;
    }

    private function descartarPreviewDaSessao(): int
    {
        $previewToken = session('fatura_preview_token');
        $preview = is_string($previewToken) ? Cache::get($previewToken) : null;
        $cartaoId = is_array($preview) ? (int) ($preview['cartao_id'] ?? 0) : 0;

        if (is_array($preview) && ! empty($preview['arquivo_path'])) {
            try {
                $this->faturaStorageService->delete((string) $preview['arquivo_path']);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        if (is_string($previewToken)) {
            Cache::forget($previewToken);
        }

        session()->forget('fatura_preview_token');

        return $cartaoId;
    }

    private function garantirUsuario(Despesa $despesa): void
    {
        abort_unless($despesa->user_id === Auth::id(), 403);
    }

    private function garantirDespesaAvulsa(Despesa $despesa): void
    {
        abort_unless(
            $despesa->tipo === 'variavel'
                && $despesa->despesa_fixa_id === null
                && $despesa->fatura_cartao_id === null
                && $despesa->origem === 'manual',
            403
        );
    }

    private function calcularValoresParcelas(float $valorTotal, int $quantidadeParcelas): array
    {
        $valorTotalCentavos = (int) round($valorTotal * 100);
        $valorBaseCentavos = intdiv($valorTotalCentavos, $quantidadeParcelas);
        $restoCentavos = $valorTotalCentavos % $quantidadeParcelas;

        $valores = [];

        for ($parcela = 1; $parcela <= $quantidadeParcelas; $parcela++) {
            $centavos = $valorBaseCentavos + ($parcela <= $restoCentavos ? 1 : 0);
            $valores[] = $centavos / 100;
        }

        return $valores;
    }
}
