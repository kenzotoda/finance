<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Nova categoria</h2>
    </x-slot>

    <div class="rounded-xl bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('categorias.store') }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="nome" value="Nome" />
                <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full" value="{{ old('nome') }}" required />
                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
            </div>
            <div class="flex gap-2">
                <a href="{{ route('categorias.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Cancelar</a>
                <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Salvar</button>
            </div>
        </form>
    </div>
</x-app-layout>
