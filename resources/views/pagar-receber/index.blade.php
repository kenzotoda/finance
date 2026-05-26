<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800">Pagar ou receber</h2>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pagar-receber.create', ['tipo' => 'pagar']) }}" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Novo a pagar
                </a>
                <a href="{{ route('pagar-receber.create', ['tipo' => 'receber']) }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    Novo a receber
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-4 shadow-sm">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('pagar-receber.index', ['filtro' => 'todos']) }}"
               class="rounded-full border px-3 py-1.5 text-xs font-medium {{ $filtro === 'todos' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                Todos
            </a>
            <a href="{{ route('pagar-receber.index', ['filtro' => 'pagar']) }}"
               class="rounded-full border px-3 py-1.5 text-xs font-medium {{ $filtro === 'pagar' ? 'border-red-600 bg-red-50 text-red-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                A pagar
            </a>
            <a href="{{ route('pagar-receber.index', ['filtro' => 'receber']) }}"
               class="rounded-full border px-3 py-1.5 text-xs font-medium {{ $filtro === 'receber' ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                A receber
            </a>
        </div>

        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('pagar-receber.limpar') }}" onsubmit="return confirm('Remover todos os lancamentos a pagar?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="escopo" value="pagar">
                <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                    Limpar a pagar
                </button>
            </form>
            <form method="POST" action="{{ route('pagar-receber.limpar') }}" onsubmit="return confirm('Remover todos os lancamentos a receber?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="escopo" value="receber">
                <button type="submit" class="rounded-md border border-emerald-200 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-50">
                    Limpar a receber
                </button>
            </form>
            <form method="POST" action="{{ route('pagar-receber.limpar') }}" onsubmit="return confirm('Remover todos os lancamentos a pagar e a receber?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="escopo" value="todos">
                <button type="submit" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                    Limpar tudo
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-7">
        @if ($filtro === 'todos' || $filtro === 'pagar')
            @include('pagar-receber._tabela', [
                'contas' => $contasPagar,
                'total' => $totalPagar,
                'tipo' => 'pagar',
                'titulo' => 'A pagar',
                'mensagemVazia' => 'Nenhum lancamento a pagar cadastrado.',
            ])
        @endif

        @if ($filtro === 'todos' || $filtro === 'receber')
            @include('pagar-receber._tabela', [
                'contas' => $contasReceber,
                'total' => $totalReceber,
                'tipo' => 'receber',
                'titulo' => 'A receber',
                'mensagemVazia' => 'Nenhum lancamento a receber cadastrado.',
            ])
        @endif
    </div>

    <div class="modal fade" id="descricaoContaModal" tabindex="-1" aria-labelledby="descricaoContaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-indigo-600">Descricao</p>
                        <h5 class="modal-title text-base font-semibold text-gray-900" id="descricaoContaModalLabel">Lancamento</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p id="descricaoContaModalTexto" class="mb-0 whitespace-pre-line break-words text-sm leading-relaxed text-gray-700"></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .conta-row {
            transition: transform 0.4s ease, opacity 0.4s ease, max-height 0.4s ease;
        }

        .conta-row.conta-row-removing {
            opacity: 0;
            transform: translateX(1.5rem);
            pointer-events: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = @json(csrf_token());

            const formatarMoeda = (valor) => (
                'R$ ' + Number(valor).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                })
            );

            const atualizarTotal = (container, valorRemovido) => {
                const totalAtual = Number(container.dataset.total || 0);
                const novoTotal = Math.max(0, totalAtual - valorRemovido);
                container.dataset.total = novoTotal.toFixed(2);

                const celulaTotal = container.querySelector('[data-total-cell]');
                if (celulaTotal) {
                    celulaTotal.textContent = formatarMoeda(novoTotal);
                }
            };

            const exibirLinhaVazia = (tbody, mensagem) => {
                if (tbody.querySelector('[data-empty-row]') || tbody.querySelector('.conta-row')) {
                    return;
                }

                const linha = document.createElement('tr');
                linha.setAttribute('data-empty-row', '');
                linha.innerHTML = `<td colspan="6" class="px-5 py-8 text-center text-gray-500">${mensagem}</td>`;
                tbody.appendChild(linha);
            };

            document.querySelectorAll('.btn-concluir-conta').forEach((botao) => {
                botao.addEventListener('click', async function () {
                    if (botao.disabled) {
                        return;
                    }

                    const linha = botao.closest('tr');
                    const container = botao.closest('[data-tabela-tipo]');
                    const tbody = container?.querySelector('[data-contas-tbody]');
                    const url = botao.dataset.url;
                    const valor = Number(botao.dataset.valor || 0);
                    const rotulo = botao.dataset.rotulo || 'Concluir';

                    if (!linha || !url || !container || !tbody) {
                        return;
                    }

                    botao.disabled = true;
                    linha.classList.add('conta-row-removing');

                    await new Promise((resolve) => {
                        window.setTimeout(resolve, 400);
                    });

                    try {
                        const resposta = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!resposta.ok) {
                            throw new Error('Erro ao concluir lancamento');
                        }

                        linha.remove();
                        atualizarTotal(container, valor);
                        exibirLinhaVazia(tbody, container.dataset.mensagemVazia || 'Nenhum lancamento cadastrado.');
                    } catch (erro) {
                        linha.classList.remove('conta-row-removing');
                        botao.disabled = false;
                        window.alert(`Nao foi possivel marcar como ${rotulo.toLowerCase()}.`);
                    }
                });
            });

            const modalElement = document.getElementById('descricaoContaModal');

            if (!modalElement) {
                return;
            }

            modalElement.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;

                if (!trigger) {
                    return;
                }

                const titulo = trigger.getAttribute('data-conta-titulo') ?? 'Lancamento';
                const descricao = trigger.getAttribute('data-conta-descricao') ?? '';
                const tituloElement = modalElement.querySelector('#descricaoContaModalLabel');
                const textoElement = modalElement.querySelector('#descricaoContaModalTexto');

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
