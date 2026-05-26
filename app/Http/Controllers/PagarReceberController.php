<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContaPagarReceberRequest;
use App\Http\Requests\UpdateContaPagarReceberRequest;
use App\Models\ContaPagarReceber;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PagarReceberController extends Controller
{
    public function index(Request $request): View
    {
        $filtro = (string) $request->query('filtro', 'todos');
        if (! in_array($filtro, ['todos', ContaPagarReceber::TIPO_PAGAR, ContaPagarReceber::TIPO_RECEBER], true)) {
            $filtro = 'todos';
        }

        $baseQuery = Auth::user()->contasPagarReceber()->with('categoria');

        $contasPagar = (clone $baseQuery)
            ->where('tipo', ContaPagarReceber::TIPO_PAGAR)
            ->orderBy('data')
            ->orderBy('id')
            ->get();

        $contasReceber = (clone $baseQuery)
            ->where('tipo', ContaPagarReceber::TIPO_RECEBER)
            ->orderBy('data')
            ->orderBy('id')
            ->get();

        $totalPagar = $contasPagar->sum('valor');
        $totalReceber = $contasReceber->sum('valor');

        return view('pagar-receber.index', compact(
            'contasPagar',
            'contasReceber',
            'totalPagar',
            'totalReceber',
            'filtro',
        ));
    }

    public function create(Request $request): View
    {
        $tipo = $request->query('tipo', ContaPagarReceber::TIPO_PAGAR);
        if (! in_array($tipo, [ContaPagarReceber::TIPO_PAGAR, ContaPagarReceber::TIPO_RECEBER], true)) {
            $tipo = ContaPagarReceber::TIPO_PAGAR;
        }

        $categorias = Auth::user()->categorias()->orderBy('nome')->get();

        return view('pagar-receber.create', compact('categorias', 'tipo'));
    }

    public function store(StoreContaPagarReceberRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $quantidadeParcelas = (int) $data['quantidade_parcelas'];
        unset($data['quantidade_parcelas']);

        $dataBase = Carbon::parse($data['data']);
        $grupoParcelamentoId = $quantidadeParcelas > 1 ? (string) Str::uuid() : null;
        $valoresParcelas = $this->calcularValoresParcelas((float) $data['valor'], $quantidadeParcelas);

        for ($parcela = 1; $parcela <= $quantidadeParcelas; $parcela++) {
            Auth::user()->contasPagarReceber()->create([
                'tipo' => $data['tipo'],
                'titulo' => $data['titulo'],
                'descricao' => $data['descricao'] ?? null,
                'categoria_id' => $data['categoria_id'] ?? null,
                'valor' => $valoresParcelas[$parcela - 1],
                'data' => $dataBase->copy()->addMonthsNoOverflow($parcela - 1)->toDateString(),
                'grupo_parcelamento_id' => $grupoParcelamentoId,
                'parcela_atual' => $quantidadeParcelas > 1 ? $parcela : null,
                'total_parcelas' => $quantidadeParcelas > 1 ? $quantidadeParcelas : null,
            ]);
        }

        $mensagem = $quantidadeParcelas > 1
            ? "Lancamento cadastrado em {$quantidadeParcelas} parcelas."
            : 'Lancamento cadastrado com sucesso.';

        return to_route('pagar-receber.index', ['filtro' => $data['tipo']])->with('success', $mensagem);
    }

    public function show(ContaPagarReceber $contaPagarReceber): RedirectResponse
    {
        $this->garantirUsuario($contaPagarReceber);

        return to_route('pagar-receber.edit', $contaPagarReceber);
    }

    public function edit(ContaPagarReceber $contaPagarReceber): View
    {
        $this->garantirUsuario($contaPagarReceber);
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();

        return view('pagar-receber.edit', compact('contaPagarReceber', 'categorias'));
    }

    public function update(UpdateContaPagarReceberRequest $request, ContaPagarReceber $contaPagarReceber): RedirectResponse
    {
        $this->garantirUsuario($contaPagarReceber);

        $contaPagarReceber->update($request->validated());

        return to_route('pagar-receber.index', ['filtro' => $contaPagarReceber->tipo])
            ->with('success', 'Lancamento atualizado com sucesso.');
    }

    public function destroy(ContaPagarReceber $contaPagarReceber): RedirectResponse|JsonResponse
    {
        $this->garantirUsuario($contaPagarReceber);

        $tipo = $contaPagarReceber->tipo;
        $valor = (float) $contaPagarReceber->valor;
        $contaPagarReceber->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'tipo' => $tipo,
                'valor' => $valor,
            ]);
        }

        return to_route('pagar-receber.index', ['filtro' => $tipo])
            ->with('success', $tipo === ContaPagarReceber::TIPO_PAGAR
                ? 'Lancamento marcado como pago.'
                : 'Lancamento marcado como recebido.');
    }

    public function destroyGrupo(string $grupoParcelamento): RedirectResponse
    {
        $contas = Auth::user()->contasPagarReceber()
            ->where('grupo_parcelamento_id', $grupoParcelamento)
            ->get();

        abort_if($contas->isEmpty(), 404);

        $tipo = $contas->first()->tipo;
        $quantidade = $contas->count();

        Auth::user()->contasPagarReceber()
            ->where('grupo_parcelamento_id', $grupoParcelamento)
            ->delete();

        return to_route('pagar-receber.index', ['filtro' => $tipo])
            ->with('success', "Grupo de {$quantidade} parcela(s) removido com sucesso.");
    }

    public function limpar(Request $request): RedirectResponse
    {
        $escopo = (string) $request->input('escopo', 'todos');
        if (! in_array($escopo, ['todos', ContaPagarReceber::TIPO_PAGAR, ContaPagarReceber::TIPO_RECEBER], true)) {
            $escopo = 'todos';
        }

        $query = Auth::user()->contasPagarReceber();
        if ($escopo !== 'todos') {
            $query->where('tipo', $escopo);
        }

        $removidos = (clone $query)->count();
        $query->delete();

        $mensagem = match ($escopo) {
            ContaPagarReceber::TIPO_PAGAR => 'Todos os lancamentos a pagar foram removidos.',
            ContaPagarReceber::TIPO_RECEBER => 'Todos os lancamentos a receber foram removidos.',
            default => 'Todos os lancamentos foram removidos.',
        };

        return to_route('pagar-receber.index', ['filtro' => $escopo === 'todos' ? 'todos' : $escopo])
            ->with('success', $removidos > 0 ? $mensagem : 'Nenhum lancamento para remover.');
    }

    private function garantirUsuario(ContaPagarReceber $contaPagarReceber): void
    {
        abort_unless($contaPagarReceber->user_id === Auth::id(), 403);
    }

    /**
     * @return array<int, float>
     */
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
