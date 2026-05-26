<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Despesas do cartao</h2>
    </x-slot>

    <div class="mb-4 grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('despesas.cartoes.store') }}">
            @csrf
            <div>
                <p class="text-sm font-semibold text-gray-800">Cartoes</p>
                <p class="mt-1 text-xs text-gray-500">Cadastre os cartoes para organizar as faturas por cartao e competencia.</p>
            </div>
            <div class="mt-3 grid gap-3 md:grid-cols-[1fr_1fr_auto] md:items-end">
                <div>
                    <x-input-label for="cartao_nome" value="Nome do cartao" />
                    <input id="cartao_nome" name="nome" type="text" class="mt-1 block w-full rounded-md border-gray-300 text-sm" placeholder="Ex.: Nubank" required>
                </div>
                <div>
                    <x-input-label for="cartao_bandeira" value="Bandeira (opcional)" />
                    <input id="cartao_bandeira" name="bandeira" type="text" class="mt-1 block w-full rounded-md border-gray-300 text-sm" placeholder="Ex.: Mastercard">
                </div>
                <button class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-900">Adicionar cartao</button>
            </div>

            </form>
        </div>

        <form method="POST" action="{{ route('despesas.importar-fatura') }}" enctype="multipart/form-data" class="rounded-xl bg-white p-4 shadow-sm">
            @csrf
            <div>
                <p class="text-sm font-semibold text-gray-800">Importar fatura</p>
                <p class="mt-1 text-xs text-gray-500">Selecione o cartao e a competencia da fatura. O mesmo cartao nao pode ter duas faturas no mesmo mes.</p>
            </div>
            <div class="mt-3 grid gap-3 md:grid-cols-3">
                <div>
                    <x-input-label for="cartao_id" value="Cartao" />
                    <select id="cartao_id" name="cartao_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm" required>
                        <option value="">Selecione</option>
                        @foreach ($cartoes as $cartao)
                            <option value="{{ $cartao->id }}" @selected((string) old('cartao_id', $selectedCartaoId) === (string) $cartao->id)>{{ $cartao->nome }}</option>
                        @endforeach
                    </select>
                </div>
                @php
                    $competenciaInput = old('competencia');
                    if (! $competenciaInput) {
                        if ($selectedCompetencia !== '') {
                            try {
                                $competenciaInput = \Carbon\Carbon::createFromFormat('Y-m', $selectedCompetencia)->format('m/Y');
                            } catch (\Throwable) {
                                $competenciaInput = now()->format('m/Y');
                            }
                        } else {
                            $competenciaInput = now()->format('m/Y');
                        }
                    }
                @endphp
                <div>
                    <x-input-label for="competencia" value="Competencia (mes/ano)" />
                    <input
                        id="competencia"
                        name="competencia"
                        type="text"
                        inputmode="numeric"
                        autocomplete="off"
                        maxlength="7"
                        placeholder="05/2026"
                        pattern="(0[1-9]|1[0-2])\/[0-9]{4}"
                        value="{{ $competenciaInput }}"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                        required
                    >
                    <x-input-error :messages="$errors->get('competencia')" class="mt-2" />
                    <p class="mt-1 text-xs text-gray-500">Formato: MM/AAAA (ex.: 05/2026)</p>
                </div>
                <div>
                    <x-input-label for="fatura" value="Arquivo" />
                    <input id="fatura" name="fatura" type="file" accept=".csv,.txt,.ofx" class="mt-1 block w-full rounded-md border-gray-300 text-sm" required>
                    <x-input-error :messages="$errors->get('fatura')" class="mt-2" />
                    <p class="mt-1 text-xs text-gray-500">
                        Formatos aceitos: <strong>OFX</strong> (exportacao do banco/cartao) ou <strong>CSV/TXT</strong> com colunas de data, descricao e valor (separador <strong>;</strong> ou <strong>,</strong>).
                        Tamanho maximo: 10 MB.
                    </p>
                </div>
            </div>
            <div class="mt-3">
                <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Analisar fatura</button>
            </div>
        </form>
    </div>

    @if (is_array($faturaPreview ?? null))
        <div class="mb-4 rounded-xl bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Previa da importacao</p>
                    <p class="text-xs text-gray-500">
                        Cartao: {{ $faturaPreview['cartao_nome'] ?? '-' }} |
                        Competencia: {{ isset($faturaPreview['competencia']) ? \Carbon\Carbon::parse($faturaPreview['competencia'])->format('m/Y') : '-' }} |
                        Arquivo: {{ $faturaPreview['arquivo_nome'] ?? '-' }} |
                        Periodo: {{ $faturaPreview['periodo_referencia'] ?? '-' }} |
                        Total: {{ $faturaPreview['total_linhas'] ?? 0 }} |
                        Prontas: {{ $faturaPreview['prontas_importacao'] ?? 0 }} |
                        Ignoradas: {{ $faturaPreview['ignoradas'] ?? 0 }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('despesas.importar-fatura.confirmar') }}">
                        @csrf
                        <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                            Confirmar importacao
                        </button>
                    </form>
                    <form method="POST" action="{{ route('despesas.importar-fatura.cancelar') }}">
                        @csrf
                        <button class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancelar importacao
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">#</th>
                            <th class="px-3 py-2 text-left">Data</th>
                            <th class="px-3 py-2 text-left">Titulo</th>
                            <th class="px-3 py-2 text-right">Valor</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach (($faturaPreview['linhas'] ?? []) as $linhaPreview)
                            <tr>
                                <td class="px-3 py-2">{{ $linhaPreview['indice'] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $linhaPreview['data'] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $linhaPreview['titulo'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-right">R$ {{ number_format((float) ($linhaPreview['valor'] ?? 0), 2, ',', '.') }}</td>
                                <td class="px-3 py-2">
                                    <span class="rounded-full px-2 py-1 {{ ($linhaPreview['status'] ?? 'ignorada') === 'pronta' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ($linhaPreview['status'] ?? 'ignorada') === 'pronta' ? 'Pronta' : 'Ignorada' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-600">{{ $linhaPreview['motivo'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mb-4 rounded-xl bg-white p-4 shadow-sm">
        <p class="text-sm font-semibold text-gray-800">Cartoes cadastrados</p>
        <p class="mt-1 text-xs text-gray-500">Selecione um cartao para ver as faturas. Ao remover um cartao, todas as faturas e lancamentos dele tambem serao excluidos.</p>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @forelse ($cartoes as $cartao)
                @php
                    $cartaoSelecionado = $selectedCartaoId === $cartao->id;
                @endphp
                <div @class([
                    'rounded-xl border-2 p-4 transition',
                    'border-indigo-500 bg-indigo-50/50 shadow-sm' => $cartaoSelecionado,
                    'border-gray-100 bg-gray-50/30 hover:border-gray-200 hover:bg-white' => ! $cartaoSelecionado,
                ])>
                    <div class="flex items-start justify-between gap-4">
                        <a href="{{ route('despesas.index', ['cartao_id' => $cartao->id]) }}"
                           class="flex min-w-0 flex-1 items-start gap-3">
                            <span @class([
                                'mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2',
                                'border-indigo-600 bg-indigo-600' => $cartaoSelecionado,
                                'border-gray-300 bg-white' => ! $cartaoSelecionado,
                            ])>
                                @if ($cartaoSelecionado)
                                    <svg class="h-3 w-3 text-white" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M2.5 6L5 8.5L9.5 3.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="min-w-0">
                                <span @class([
                                    'block truncate text-sm font-semibold',
                                    'text-indigo-900' => $cartaoSelecionado,
                                    'text-gray-900' => ! $cartaoSelecionado,
                                ])>{{ $cartao->nome }}</span>
                                @if ($cartao->bandeira)
                                    <span class="mt-0.5 block text-xs text-gray-500">{{ $cartao->bandeira }}</span>
                                @endif
                                @if ($cartao->faturas_count > 0)
                                    <span class="mt-1 block text-xs text-gray-500">
                                        {{ $cartao->faturas_count }} {{ $cartao->faturas_count === 1 ? 'fatura importada' : 'faturas importadas' }}
                                    </span>
                                @else
                                    <span class="mt-1 block text-xs text-gray-400">Nenhuma fatura importada</span>
                                @endif
                            </span>
                        </a>

                        <form method="POST" action="{{ route('despesas.cartoes.destroy', $cartao) }}" onsubmit="return confirm(@js(
                            'Remover o cartao '.$cartao->nome.'?'.
                            ($cartao->faturas_count > 0
                                ? ' Todas as '.$cartao->faturas_count.' fatura(s) e lancamentos serao excluidos permanentemente.'
                                : '').
                            ' Esta acao nao pode ser desfeita.'
                        ))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="shrink-0 rounded-md border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                                Remover
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 md:col-span-2">Cadastre um cartao para organizar as faturas.</p>
            @endforelse
        </div>
    </div>

    @if ($selectedCartaoId > 0)
        <div class="mb-4 rounded-xl bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('despesas.index', ['cartao_id' => $selectedCartaoId]) }}"
                   class="rounded-full border px-3 py-1 text-xs {{ $selectedCompetencia === '' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                    Todos os meses
                </a>
                @foreach ($competenciasDisponiveis as $item)
                    <a href="{{ route('despesas.index', ['cartao_id' => $selectedCartaoId, 'competencia' => $item['value']]) }}"
                       class="rounded-full border px-3 py-1 text-xs {{ $selectedCompetencia === $item['value'] ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($faturas as $fatura)
            <div class="rounded-xl bg-white p-4 shadow-sm">
                <p class="text-sm font-semibold text-gray-900">{{ $fatura->competencia->format('m/Y') }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $fatura->arquivo_nome }}</p>
                <div class="mt-3 space-y-1 text-sm">
                    <p class="text-gray-700">Lancamentos: <span class="font-medium">{{ $fatura->despesas_count }}</span></p>
                    <p class="text-gray-700">Total: <span class="font-semibold text-red-700">R$ {{ number_format((float) ($fatura->despesas_sum_valor ?? 0), 2, ',', '.') }}</span></p>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('despesas.faturas.show', $fatura) }}" class="inline-flex rounded border border-gray-300 px-3 py-1.5 text-xs hover:bg-gray-50">Ver detalhes</a>
                    <form method="POST" action="{{ route('despesas.faturas.destroy', $fatura) }}" onsubmit="return confirm('Excluir esta fatura? Todos os lancamentos serao removidos permanentemente.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex rounded border border-red-300 px-3 py-1.5 text-xs text-red-700 hover:bg-red-50">Excluir fatura</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-xl bg-white p-6 text-center text-sm text-gray-500 shadow-sm md:col-span-2 xl:col-span-3">
                Nenhuma fatura encontrada para este cartao/mes.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $faturas->links() }}</div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('competencia');
            if (!input) {
                return;
            }

            const formatCompetencia = (value) => {
                const digits = value.replace(/\D/g, '').slice(0, 6);

                if (digits.length <= 2) {
                    return digits;
                }

                return `${digits.slice(0, 2)}/${digits.slice(2)}`;
            };

            const isControlKey = (event) => (
                ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)
                || (event.ctrlKey || event.metaKey)
            );

            input.addEventListener('keydown', (event) => {
                if (isControlKey(event)) {
                    return;
                }

                if (!/^[0-9/]$/.test(event.key)) {
                    event.preventDefault();
                }
            });

            input.addEventListener('input', () => {
                input.value = formatCompetencia(input.value);
            });

            input.addEventListener('blur', () => {
                const match = input.value.match(/^(0[1-9]|1[0-2])\/(\d{4})$/);
                if (!match) {
                    input.setCustomValidity('Informe a competencia no formato MM/AAAA (ex.: 05/2026).');
                    return;
                }

                input.setCustomValidity('');
            });
        });
    </script>
</x-app-layout>
