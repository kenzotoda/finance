<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Despesas fixas</h2>
            <a href="{{ route('despesas-fixas.create') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Nova despesa fixa</a>
        </div>
    </x-slot>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Titulo</th>
                    <th class="px-4 py-3 text-left">Categoria</th>
                    <th class="px-4 py-3 text-left">Periodicidade</th>
                    <th class="px-4 py-3 text-left">Vencimento / Renovacao</th>
                    <th class="px-4 py-3 text-right">Valor</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($despesasFixas as $despesaFixa)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="inline-flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $despesaFixa->titulo }}</span>
                                @if ($despesaFixa->descricao)
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#descricaoDespesaModal" data-despesa-titulo="{{ $despesaFixa->titulo }}" data-despesa-descricao="{{ $despesaFixa->descricao }}" class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-sky-300 bg-sky-50 text-[11px] font-bold text-sky-700 transition hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-500" aria-label="Abrir descricao da despesa">
                                        i
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $despesaFixa->categoria?->nome ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs {{ $despesaFixa->periodicidade === 'anual' ? 'bg-violet-100 text-violet-700' : 'bg-sky-100 text-sky-700' }}">
                                {{ $despesaFixa->periodicidade === 'anual' ? 'Anual' : 'Mensal' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($despesaFixa->periodicidade === 'anual')
                                Renovacao {{ str_pad((string) ($despesaFixa->renovacao_mes ?? 0), 2, '0', STR_PAD_LEFT) }}/{{ $despesaFixa->renovacao_ano ?? '----' }}
                            @else
                                Dia {{ $despesaFixa->dia_vencimento }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-sky-700">R$ {{ number_format($despesaFixa->valor, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="rounded-full px-2 py-1 text-xs {{ $despesaFixa->ativa ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $despesaFixa->ativa ? 'Ativa' : 'Inativa' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('despesas-fixas.edit', $despesaFixa) }}" class="rounded border border-gray-300 px-3 py-1.5 text-xs hover:bg-gray-50">Editar</a>
                                <form method="POST" action="{{ route('despesas-fixas.destroy', $despesaFixa) }}" onsubmit="return confirm('Deseja excluir esta despesa fixa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded border border-red-300 px-3 py-1.5 text-xs text-red-700 hover:bg-red-50">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">Nenhuma despesa fixa cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total das despesas fixas</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-sky-800">R$ {{ number_format($totalDespesasFixas, 2, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-4">{{ $despesasFixas->links() }}</div>

    <div class="modal fade" id="descricaoDespesaModal" tabindex="-1" aria-labelledby="descricaoDespesaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-sky-600">Descricao da despesa</p>
                        <h5 class="modal-title text-base font-semibold text-gray-900" id="descricaoDespesaModalLabel">Despesa fixa</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p id="descricaoDespesaModalTexto" class="mb-0 whitespace-pre-line break-words text-sm leading-relaxed text-gray-700"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('descricaoDespesaModal');

            if (!modalElement) {
                return;
            }

            modalElement.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;

                if (!trigger) {
                    return;
                }

                const titulo = trigger.getAttribute('data-despesa-titulo') ?? 'Despesa fixa';
                const descricao = trigger.getAttribute('data-despesa-descricao') ?? '';
                const tituloElement = modalElement.querySelector('#descricaoDespesaModalLabel');
                const textoElement = modalElement.querySelector('#descricaoDespesaModalTexto');

                if (tituloElement) {
                    tituloElement.textContent = titulo;
                }

                if (textoElement) {
                    textoElement.textContent = descricao;
                }
            });
        });
    </script>
</x-app-layout>
