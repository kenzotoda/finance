<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Editar receita</h2>
    </x-slot>

    <div class="rounded-xl bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('receitas.update', $receita) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('receitas._form')
            <div class="flex gap-2">
                <a href="{{ route('receitas.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Cancelar</a>
                <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Atualizar</button>
            </div>
        </form>
    </div>
</x-app-layout>
