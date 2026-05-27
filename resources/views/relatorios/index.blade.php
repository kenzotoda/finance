<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Relatorio mensal</h2>
    </x-slot>

    <form method="GET" class="mb-4 grid gap-3 rounded-xl bg-white p-4 shadow-sm md:grid-cols-2">
        <select name="competencia" class="rounded-md border-gray-300 text-sm">
            @foreach ($mesesDisponiveis as $item)
                <option value="{{ $item['value'] }}" @selected($item['value'] === $competencia)>{{ $item['label'] }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">Ver mes</button>
    </form>

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('relatorios.export.excel', request()->query()) }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            Exportar Excel
        </a>
        <a href="{{ route('relatorios.export.pdf', request()->query()) }}" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
            Exportar PDF
        </a>
    </div>

    <p class="mb-4 text-sm text-gray-600">Competencia selecionada: <span class="font-semibold">{{ $competenciaLabel }}</span></p>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Receita mensal</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">R$ {{ number_format($totalReceitas, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total despesas</p>
            <p class="mt-2 text-2xl font-bold text-red-600">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Saldo</p>
            <p class="mt-2 text-2xl font-bold {{ $saldo >= 0 ? 'text-indigo-600' : 'text-amber-600' }}">R$ {{ number_format($saldo, 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3">
            <h3 class="font-semibold text-gray-800">Lancamentos do mes</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Título</th>
                    <th class="px-4 py-3 text-left">Categoria</th>
                    <th class="px-4 py-3 text-right">Valor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($lancamentos as $lancamento)
                    <tr>
                        <td class="px-4 py-3">{{ $lancamento['tipo'] }}</td>
                        <td class="px-4 py-3">{{ $lancamento['data'] }}</td>
                        <td class="px-4 py-3">{{ $lancamento['titulo'] }}</td>
                        <td class="px-4 py-3">{{ $lancamento['categoria'] ?: '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $lancamento['valor'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            R$ {{ number_format($lancamento['valor'], 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Nenhum lancamento encontrado para o filtro.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
