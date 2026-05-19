<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Nova despesa fixa</h2>
    </x-slot>

    <div class="rounded-xl bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('despesas-fixas.store') }}" class="space-y-4">
            @csrf
            @include('despesas-fixas._form')
            <div class="flex gap-2">
                <a href="{{ route('despesas-fixas.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Cancelar</a>
                <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Salvar</button>
            </div>
        </form>
    </div>
</x-app-layout>
