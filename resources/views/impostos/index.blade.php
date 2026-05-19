<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Impostos</h2>
            <a href="{{ route('impostos.create') }}" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Novo imposto</a>
        </div>
    </x-slot>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Titulo</th>
                    <th class="px-4 py-3 text-left">Periodicidade</th>
                    <th class="px-4 py-3 text-left">Vencimento / Renovacao</th>
                    <th class="px-4 py-3 text-right">Valor</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($impostos as $imposto)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-800">{{ $imposto->tipoLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="inline-flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $imposto->titulo }}</span>
                                @if ($imposto->descricao)
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#descricaoImpostoModal" data-imposto-titulo="{{ $imposto->titulo }}" data-imposto-descricao="{{ $imposto->descricao }}" class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-amber-300 bg-amber-50 text-[11px] font-bold text-amber-700 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500" aria-label="Abrir observacoes">
                                        i
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs {{ $imposto->periodicidade === 'anual' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $imposto->periodicidade === 'anual' ? 'Anual' : 'Mensal' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($imposto->periodicidade === 'anual')
                                {{ str_pad((string) ($imposto->renovacao_mes ?? 0), 2, '0', STR_PAD_LEFT) }}/{{ $imposto->renovacao_ano ?? '----' }}
                            @else
                                Dia {{ $imposto->dia_vencimento }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-amber-700">R$ {{ number_format($imposto->valor, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="rounded-full px-2 py-1 text-xs {{ $imposto->ativa ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $imposto->ativa ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('impostos.edit', $imposto) }}" class="rounded border border-gray-300 px-3 py-1.5 text-xs hover:bg-gray-50">Editar</a>
                                <form method="POST" action="{{ route('impostos.destroy', $imposto) }}" onsubmit="return confirm('Deseja excluir este imposto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded border border-red-300 px-3 py-1.5 text-xs text-red-700 hover:bg-red-50">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">Nenhum imposto cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total dos impostos</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-amber-800">R$ {{ number_format($totalImpostos, 2, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-4">{{ $impostos->links() }}</div>

    <div class="modal fade" id="descricaoImpostoModal" tabindex="-1" aria-labelledby="descricaoImpostoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-amber-600">Observacoes</p>
                        <h5 class="modal-title text-base font-semibold text-gray-900" id="descricaoImpostoModalLabel">Imposto</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p id="descricaoImpostoModalTexto" class="mb-0 whitespace-pre-line break-words text-sm leading-relaxed text-gray-700"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('descricaoImpostoModal');
            if (!modalElement) return;

            modalElement.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                const titulo = trigger.getAttribute('data-imposto-titulo') ?? 'Imposto';
                const descricao = trigger.getAttribute('data-imposto-descricao') ?? '';
                const tituloElement = modalElement.querySelector('#descricaoImpostoModalLabel');
                const textoElement = modalElement.querySelector('#descricaoImpostoModalTexto');

                if (tituloElement) tituloElement.textContent = titulo;
                if (textoElement) textoElement.textContent = descricao;
            });
        });
    </script>
</x-app-layout>
