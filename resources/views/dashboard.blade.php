<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-slate-800">Dashboard</h2>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">
                Visao geral
            </span>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('lucros-fixos.index') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-emerald-100 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm text-slate-500">Lucros fixos mensais</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">R$ {{ number_format($totalLucrosFixosMensais, 2, ',', '.') }}</p>
        </a>
        <a href="{{ route('despesas-fixas.index') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-sky-100 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm text-slate-500">Despesas fixas mensais</p>
            <p class="mt-2 text-2xl font-bold text-sky-700">R$ {{ number_format($totalDespesasFixasMensais, 2, ',', '.') }}</p>
        </a>
        <a href="{{ route('impostos.index') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-amber-100 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm text-slate-500">Impostos mensais</p>
            <p class="mt-2 text-2xl font-bold text-amber-700">R$ {{ number_format($totalImpostosMensais, 2, ',', '.') }}</p>
        </a>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-blue-100">
            <p class="text-sm text-slate-500">Saldo estimado mensal</p>
            <p class="mt-2 text-2xl font-bold {{ $saldoMensal >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                R$ {{ number_format($saldoMensal, 2, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-slate-400">Lucros mensais − despesas mensais − impostos mensais</p>
        </div>
    </div>
</x-app-layout>
