@php
    $corValor = $tipo === 'pagar' ? 'text-red-700' : 'text-emerald-700';
    $corTotal = $tipo === 'pagar' ? 'text-red-800' : 'text-emerald-800';
    $corBarra = $tipo === 'pagar' ? 'bg-red-600' : 'bg-emerald-600';
    $rotuloConcluir = $tipo === 'pagar' ? 'Pago' : 'Recebido';
    $btnConcluirClasses = $tipo === 'pagar'
        ? 'border-sky-200 text-sky-700 hover:border-sky-300 hover:bg-sky-50'
        : 'border-emerald-200 text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50';
@endphp

<div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-blue-100">
    <div class="flex items-center gap-3 bg-blue-50 px-5 py-4">
        <span class="h-8 w-1 shrink-0 rounded-full {{ $corBarra }}" aria-hidden="true"></span>
        <h3 class="text-lg font-bold tracking-tight text-gray-900">{{ $titulo }}</h3>
    </div>
    <div class="overflow-x-auto px-5 pt-3 pb-4" data-tabela-tipo="{{ $tipo }}" data-total="{{ $total }}" data-mensagem-vazia="{{ $mensagemVazia }}">
        <table class="w-full min-w-[960px] table-fixed divide-y divide-gray-200 text-sm">
            <colgroup>
                <col style="width: 26%;">
                <col style="width: 16%;">
                <col style="width: 14%;">
                <col style="width: 12%;">
                <col style="width: 13%;">
                <col style="width: 19%;">
            </colgroup>
            <thead class="bg-blue-100/70">
                <tr>
                    <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Título</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Categoria</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Data</th>
                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-700">Parcela</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-700">Valor</th>
                    <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-700">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white" data-contas-tbody>
                @forelse ($contas as $conta)
                    <tr class="conta-row align-middle" data-conta-id="{{ $conta->id }}">
                        <td class="px-5 py-4">
                            <div class="inline-flex max-w-full items-center gap-2">
                                <span class="truncate font-medium text-gray-900">{{ $conta->titulo }}</span>
                                @if ($conta->descricao)
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#descricaoContaModal" data-conta-titulo="{{ $conta->titulo }}" data-conta-descricao="{{ $conta->descricao }}" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-blue-300 bg-blue-50 p-0 text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1" aria-label="Abrir descricao">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.25v3.5a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75H9z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="truncate px-4 py-4 text-gray-700">{{ $conta->categoria?->nome ?? '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-gray-700">{{ $conta->data->format('d/m/Y') }}</td>
                        <td class="px-4 py-4 text-center text-gray-700">
                            @if ($conta->isParcelada())
                                {{ $conta->parcela_atual }}/{{ $conta->total_parcelas }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-right font-semibold tabular-nums {{ $corValor }}">
                            R$ {{ number_format($conta->valor, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                <a href="{{ route('pagar-receber.edit', $conta) }}" class="inline-flex h-8 min-w-[4.25rem] items-center justify-center rounded-md border border-blue-200 bg-white px-3 text-xs font-medium text-blue-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50">
                                    Editar
                                </a>
                                <button
                                    type="button"
                                    class="btn-concluir-conta inline-flex h-8 min-w-[4.75rem] items-center justify-center rounded-md border bg-white px-3 text-xs font-medium shadow-sm transition disabled:cursor-not-allowed disabled:opacity-60 {{ $btnConcluirClasses }}"
                                    data-url="{{ route('pagar-receber.destroy', $conta) }}"
                                    data-valor="{{ $conta->valor }}"
                                    data-rotulo="{{ $rotuloConcluir }}"
                                >
                                    {{ $rotuloConcluir }}
                                </button>
                                @if ($conta->isParcelada())
                                    <form method="POST" action="{{ route('pagar-receber.grupo.destroy', $conta->grupo_parcelamento_id) }}" class="inline-flex" onsubmit="return confirm('Excluir todas as {{ $conta->total_parcelas }} parcelas deste lancamento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 items-center justify-center rounded-md border border-amber-200 bg-white px-3 text-xs font-medium text-amber-700 shadow-sm transition hover:border-amber-300 hover:bg-amber-50">
                                            Excluir parcelas
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr data-empty-row>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-500">{{ $mensagemVazia }}</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t border-blue-100 bg-blue-50/70">
                <tr>
                    <td colspan="4" class="px-5 py-3.5 text-right text-sm font-semibold text-gray-700">Total</td>
                    <td class="whitespace-nowrap px-4 py-3.5 text-right text-sm font-bold tabular-nums {{ $corTotal }}" data-total-cell>
                        R$ {{ number_format($total, 2, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
