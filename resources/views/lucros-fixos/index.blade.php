<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Lucros fixos</h2>
            <a href="{{ route('lucros-fixos.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Novo lucro fixo</a>
        </div>
    </x-slot>

    <div class="space-y-7">
        @include('lucros-fixos._tabela', [
            'lucrosFixos' => $lucrosFixosMensais,
            'total' => $totalLucrosFixosMensais,
            'tipo' => 'mensal',
            'titulo' => 'Lucros fixos mensais',
            'mensagemVazia' => 'Nenhum lucro fixo mensal cadastrado.',
        ])

        @include('lucros-fixos._tabela', [
            'lucrosFixos' => $lucrosFixosAnuais,
            'total' => $totalLucrosFixosAnuais,
            'tipo' => 'anual',
            'titulo' => 'Lucros fixos anuais',
            'mensagemVazia' => 'Nenhum lucro fixo anual cadastrado.',
        ])
    </div>

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
