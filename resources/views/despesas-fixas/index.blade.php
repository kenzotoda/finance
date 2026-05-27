<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-800">Despesas fixas</h2>
            <a href="{{ route('despesas-fixas.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">Nova despesa fixa</a>
        </div>
    </x-slot>

    <div class="finance-stacked-sections">
        @include('despesas-fixas._tabela', [
            'despesasFixas' => $despesasFixasMensais,
            'total' => $totalDespesasFixasMensais,
            'tipo' => 'mensal',
            'titulo' => 'Despesas fixas mensais',
            'mensagemVazia' => 'Nenhuma despesa fixa mensal cadastrada.',
        ])

        @include('despesas-fixas._tabela', [
            'despesasFixas' => $despesasFixasAnuais,
            'total' => $totalDespesasFixasAnuais,
            'tipo' => 'anual',
            'titulo' => 'Despesas fixas anuais',
            'mensagemVazia' => 'Nenhuma despesa fixa anual cadastrada.',
        ])
    </div>

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
