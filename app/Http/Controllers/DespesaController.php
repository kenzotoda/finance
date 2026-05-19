<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDespesaRequest;
use App\Http\Requests\UpdateDespesaRequest;
use App\Models\Despesa;
use App\Services\ReplicarDespesasFixasService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DespesaController extends Controller
{
    public function __construct(private readonly ReplicarDespesasFixasService $replicarDespesasFixasService) {}

    public function index(): View
    {
        $user = Auth::user();
        $this->replicarDespesasFixasService->execute($user);

        $mes = (int) request('mes', now()->month);
        $ano = (int) request('ano', now()->year);
        $categoriaId = request('categoria_id');

        $despesas = $user->despesas()
            ->with('categoria')
            ->where('tipo', 'variavel')
            ->when($mes, fn (Builder $query) => $query->whereMonth('data', $mes))
            ->when($ano, fn (Builder $query) => $query->whereYear('data', $ano))
            ->when($categoriaId, fn (Builder $query) => $query->where('categoria_id', $categoriaId))
            ->orderByDesc('data')
            ->paginate(10)
            ->withQueryString();

        $categorias = $user->categorias()->orderBy('nome')->get();

        return view('despesas.index', compact('despesas', 'categorias', 'mes', 'ano', 'categoriaId'));
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
        $data['despesa_fixa_id'] = null;
        $data['compra_parcelada_id'] = null;
        $data['parcela_atual'] = null;
        $data['total_parcelas'] = null;

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
        $data['despesa_fixa_id'] = null;

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

    private function garantirUsuario(Despesa $despesa): void
    {
        abort_unless($despesa->user_id === Auth::id(), 403);
    }

    private function garantirDespesaAvulsa(Despesa $despesa): void
    {
        abort_unless($despesa->tipo === 'variavel' && $despesa->despesa_fixa_id === null, 403);
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
