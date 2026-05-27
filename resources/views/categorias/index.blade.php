<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-800">Categorias</h2>
            <a href="{{ route('categorias.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">Nova categoria</a>
        </div>
    </x-slot>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nome</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($categorias as $categoria)
                    <tr>
                        <td class="px-4 py-3">{{ $categoria->nome }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('categorias.edit', $categoria) }}" class="rounded border border-gray-300 px-3 py-1.5 text-xs hover:bg-gray-50">Editar</a>
                                <form method="POST" action="{{ route('categorias.destroy', $categoria) }}" onsubmit="return confirm('Deseja excluir esta categoria?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded border border-red-300 px-3 py-1.5 text-xs text-red-700 hover:bg-red-50">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-gray-500">Nenhuma categoria cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categorias->links() }}</div>
</x-app-layout>
