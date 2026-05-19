<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Lucros fixos</h2>
            <a href="{{ route('lucros-fixos.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Novo lucro fixo</a>
        </div>
    </x-slot>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Titulo</th>
                    <th class="px-4 py-3 text-left">Categoria</th>
                    <th class="px-4 py-3 text-left">Periodicidade</th>
                    <th class="px-4 py-3 text-left">Recebimento / Renovacao</th>
                    <th class="px-4 py-3 text-right">Valor</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($lucrosFixos as $lucroFixo)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="inline-flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $lucroFixo->titulo }}</span>
                                @if ($lucroFixo->descricao)
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#descricaoLucroModal" data-lucro-titulo="{{ $lucroFixo->titulo }}" data-lucro-descricao="{{ $lucroFixo->descricao }}" class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-emerald-300 bg-emerald-50 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-label="Abrir descricao do lucro">
                                        i
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $lucroFixo->categoria?->nome ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs {{ $lucroFixo->periodicidade === 'anual' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $lucroFixo->periodicidade === 'anual' ? 'Anual' : 'Mensal' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($lucroFixo->periodicidade === 'anual')
                                Renovacao {{ str_pad((string) ($lucroFixo->renovacao_mes ?? 0), 2, '0', STR_PAD_LEFT) }}/{{ $lucroFixo->renovacao_ano ?? '----' }}
                            @else
                                Dia {{ $lucroFixo->dia_vencimento }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-emerald-700">R$ {{ number_format($lucroFixo->valor, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="rounded-full px-2 py-1 text-xs {{ $lucroFixo->ativa ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $lucroFixo->ativa ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('lucros-fixos.edit', $lucroFixo) }}" class="rounded border border-gray-300 px-3 py-1.5 text-xs hover:bg-gray-50">Editar</a>
                                <form method="POST" action="{{ route('lucros-fixos.destroy', $lucroFixo) }}" onsubmit="return confirm('Deseja excluir este lucro fixo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded border border-red-300 px-3 py-1.5 text-xs text-red-700 hover:bg-red-50">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">Nenhum lucro fixo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total dos lucros fixos</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-emerald-800">R$ {{ number_format($totalLucrosFixos, 2, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-4">{{ $lucrosFixos->links() }}</div>

    <div class="modal fade" id="descricaoLucroModal" tabindex="-1" aria-labelledby="descricaoLucroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-emerald-600">Descricao do lucro</p>
                        <h5 class="modal-title text-base font-semibold text-gray-900" id="descricaoLucroModalLabel">Lucro fixo</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p id="descricaoLucroModalTexto" class="mb-0 whitespace-pre-line break-words text-sm leading-relaxed text-gray-700"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('descricaoLucroModal');

            if (!modalElement) {
                return;
            }

            modalElement.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;

                if (!trigger) {
                    return;
                }

                const titulo = trigger.getAttribute('data-lucro-titulo') ?? 'Lucro fixo';
                const descricao = trigger.getAttribute('data-lucro-descricao') ?? '';
                const tituloElement = modalElement.querySelector('#descricaoLucroModalLabel');
                const textoElement = modalElement.querySelector('#descricaoLucroModalTexto');

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
