<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('lucros-fixos.index') }}" class="rounded-xl bg-white p-5 shadow-sm transition hover:shadow-md">
            <p class="text-sm text-gray-500">Lucros fixos mensais</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">R$ {{ number_format($totalLucrosFixosMensais, 2, ',', '.') }}</p>
        </a>
        <a href="{{ route('despesas-fixas.index') }}" class="rounded-xl bg-white p-5 shadow-sm transition hover:shadow-md">
            <p class="text-sm text-gray-500">Despesas fixas mensais</p>
            <p class="mt-2 text-2xl font-bold text-sky-700">R$ {{ number_format($totalDespesasFixasMensais, 2, ',', '.') }}</p>
        </a>
        <a href="{{ route('impostos.index') }}" class="rounded-xl bg-white p-5 shadow-sm transition hover:shadow-md">
            <p class="text-sm text-gray-500">Impostos mensais</p>
            <p class="mt-2 text-2xl font-bold text-amber-700">R$ {{ number_format($totalImpostosMensais, 2, ',', '.') }}</p>
        </a>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Saldo estimado mensal</p>
            <p class="mt-2 text-2xl font-bold {{ $saldoMensal >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                R$ {{ number_format($saldoMensal, 2, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-gray-400">Lucros mensais − despesas mensais − impostos mensais</p>
        </div>
    </div>
</x-app-layout>
