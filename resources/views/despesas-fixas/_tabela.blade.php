<div class="overflow-hidden rounded-xl bg-white shadow-sm">
    <div class="flex items-center gap-3 bg-gray-50 px-5 py-4">
        <span class="h-8 w-1 shrink-0 rounded-full bg-sky-600" aria-hidden="true"></span>
        <h3 class="text-lg font-bold tracking-tight text-gray-900">{{ $titulo }}</h3>
    </div>
    <div class="overflow-x-auto px-5 pt-3 pb-4">
        <table class="w-full min-w-[960px] table-fixed divide-y divide-gray-200 text-sm">
            <colgroup>
                <col style="width: 28%;">
                <col style="width: 17%;">
                <col style="width: 14%;">
                <col style="width: 13%;">
                <col style="width: 12%;">
                <col style="width: 16%;">
            </colgroup>
            <thead class="bg-gray-100">
                <tr>
                    <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Titulo</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">Categoria</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700">{{ $tipo === 'anual' ? 'Renovacao' : 'Vencimento' }}</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-700">Valor</th>
                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-700">Status</th>
                    <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-700">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($despesasFixas as $despesaFixa)
                    <tr class="align-middle">
                        <td class="px-5 py-4">
                            <div class="inline-flex max-w-full items-center gap-2">
                                <span class="truncate font-medium text-gray-900">{{ $despesaFixa->titulo }}</span>
                                @if ($despesaFixa->descricao)
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#descricaoDespesaModal" data-despesa-titulo="{{ $despesaFixa->titulo }}" data-despesa-descricao="{{ $despesaFixa->descricao }}" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-sky-300 bg-sky-50 p-0 text-sky-700 transition hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-1" aria-label="Abrir descricao da despesa">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.25v3.5a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75H9z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="truncate px-4 py-4 text-gray-700">{{ $despesaFixa->categoria?->nome ?? '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-gray-700">
                            @if ($tipo === 'anual')
                                Renovacao {{ str_pad((string) ($despesaFixa->renovacao_mes ?? 0), 2, '0', STR_PAD_LEFT) }}/{{ $despesaFixa->renovacao_ano ?? '----' }}
                            @else
                                Dia {{ $despesaFixa->dia_vencimento }}
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-right font-semibold tabular-nums text-sky-700">R$ {{ number_format($despesaFixa->valor, 2, ',', '.') }}</td>
                        <td class="px-4 py-4 text-center align-middle">
                            <span @class([
                                'relative inline-flex h-6 min-w-[5rem] shrink-0 items-center justify-center rounded-full border px-3 text-xs font-medium !leading-none',
                                'border-emerald-200 bg-emerald-50 text-emerald-800' => $despesaFixa->ativa,
                                'border-gray-200 bg-gray-50 text-gray-600' => ! $despesaFixa->ativa,
                            ])>
                                <span @class([
                                    'absolute left-2 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full',
                                    'bg-emerald-500' => $despesaFixa->ativa,
                                    'bg-gray-400' => ! $despesaFixa->ativa,
                                ]) aria-hidden="true"></span>
                                <span class="block !m-0 text-center !leading-none">{{ $despesaFixa->ativa ? 'Ativa' : 'Inativa' }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center justify-end gap-2">
                                <a href="{{ route('despesas-fixas.edit', $despesaFixa) }}" class="inline-flex h-8 min-w-[4.25rem] items-center justify-center rounded-md border border-gray-300 bg-white px-3 text-xs font-medium text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('despesas-fixas.destroy', $despesaFixa) }}" class="inline-flex" onsubmit="return confirm('Deseja excluir esta despesa fixa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 min-w-[4.25rem] items-center justify-center rounded-md border border-red-200 bg-white px-3 text-xs font-medium text-red-600 shadow-sm transition hover:border-red-300 hover:bg-red-50">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-500">{{ $mensagemVazia }}</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t border-gray-200 bg-gray-50">
                <tr>
                    <td colspan="3" class="px-5 py-3.5 text-right text-sm font-semibold text-gray-700">
                        Total {{ $tipo === 'anual' ? 'anual' : 'mensal' }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-3.5 text-right text-sm font-bold tabular-nums text-sky-800">R$ {{ number_format($total, 2, ',', '.') }}</td>
                    <td colspan="2" class="px-5 py-3.5"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
