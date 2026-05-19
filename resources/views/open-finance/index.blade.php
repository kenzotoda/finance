<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Dashboard Open Finance</h2>
            <button id="connect-bank-btn" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Conectar Banco
            </button>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Bancos conectados</p>
            <p class="mt-2 text-2xl font-bold text-indigo-700">{{ $items->count() }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Contas bancarias</p>
            <p class="mt-2 text-2xl font-bold text-sky-700">{{ $accountsCount }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Transacoes sincronizadas</p>
            <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $transactionsCount }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Saldo transacional</p>
            <p class="mt-2 text-2xl font-bold {{ ($totalCredit + $totalDebit) >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                R$ {{ number_format($totalCredit + $totalDebit, 2, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Instituicao</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Contas</th>
                    <th class="px-4 py-3 text-left">Ultima sincronizacao</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item->connector_name ?? 'Banco' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs {{ $item->status === 'UPDATED' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $item->status ?? 'Pendente' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $item->accounts->count() }}</td>
                        <td class="px-4 py-3">{{ $item->last_synced_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('open-finance.items.sync', $item) }}">
                                @csrf
                                <button class="rounded border border-indigo-300 px-3 py-1.5 text-xs text-indigo-700 hover:bg-indigo-50">
                                    Sincronizar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Nenhum banco conectado ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.pluggy.ai/pluggy-connect/v2.7.0/pluggy-connect.js"></script>
    <script>
        function createPluggyInstance(options) {
            const Candidate = window.PluggyConnect?.default ?? window.PluggyConnect;

            if (typeof Candidate === 'function') {
                return new Candidate(options);
            }

            if (window.PluggyConnect && typeof window.PluggyConnect.init === 'function') {
                return window.PluggyConnect.init(options);
            }

            throw new Error('SDK da Pluggy nao carregado corretamente.');
        }

        async function abrirPluggyConnect() {
            const response = await fetch('{{ route('open-finance.connect-token') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            });

            if (! response.ok) {
                throw new Error('Nao foi possivel gerar token da Pluggy.');
            }

            const { accessToken } = await response.json();
            const pluggyConnect = createPluggyInstance({
                connectToken: accessToken,
                includeSandbox: {{ config('services.pluggy.include_sandbox') ? 'true' : 'false' }},
                onSuccess: async (itemData) => {
                    await fetch('{{ route('open-finance.items.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ item_id: itemData.item.id }),
                    });

                    window.location.reload();
                },
                onError: (error) => {
                    console.error('Erro ao conectar banco na Pluggy', error);
                    alert('Falha ao conectar banco. Tente novamente.');
                },
            });

            if (pluggyConnect && typeof pluggyConnect.init === 'function') {
                pluggyConnect.init();
            }
        }

        document.getElementById('connect-bank-btn')?.addEventListener('click', () => {
            abrirPluggyConnect().catch((error) => {
                console.error(error);
                alert('Erro ao inicializar Open Finance.');
            });
        });
    </script>
</x-app-layout>
