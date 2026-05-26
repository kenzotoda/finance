<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Impostos</h2>
            <a href="{{ route('impostos.create') }}" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Novo imposto</a>
        </div>
    </x-slot>

    <div class="space-y-7">
        @include('impostos._tabela', [
            'impostos' => $impostosMensais,
            'total' => $totalImpostosMensais,
            'tipo' => 'mensal',
            'titulo' => 'Impostos mensais',
            'mensagemVazia' => 'Nenhum imposto mensal cadastrado.',
        ])

        @include('impostos._tabela', [
            'impostos' => $impostosAnuais,
            'total' => $totalImpostosAnuais,
            'tipo' => 'anual',
            'titulo' => 'Impostos anuais',
            'mensagemVazia' => 'Nenhum imposto anual cadastrado.',
        ])
    </div>

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

            if (!modalElement) {
                return;
            }

            modalElement.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;

                if (!trigger) {
                    return;
                }

                const titulo = trigger.getAttribute('data-imposto-titulo') ?? 'Imposto';
                const descricao = trigger.getAttribute('data-imposto-descricao') ?? '';
                const tituloElement = modalElement.querySelector('#descricaoImpostoModalLabel');
                const textoElement = modalElement.querySelector('#descricaoImpostoModalTexto');

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
