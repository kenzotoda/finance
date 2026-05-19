<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Despesas</h2>
            <a href="{{ route('despesas.create') }}" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Nova despesa</a>
        </div>
    </x-slot>

    <form method="GET" class="mb-4 grid gap-3 rounded-xl bg-white p-4 shadow-sm md:grid-cols-4">
        <input type="number" name="mes" min="1" max="12" value="{{ $mes }}" class="rounded-md border-gray-300 text-sm" placeholder="Mes">
        <input type="number" name="ano" min="2000" max="2100" value="{{ $ano }}" class="rounded-md border-gray-300 text-sm" placeholder="Ano">
        <select name="categoria_id" class="rounded-md border-gray-300 text-sm">
            <option value="">Todas categorias</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected((string) $categoriaId === (string) $categoria->id)>{{ $categoria->nome }}</option>
            @endforeach
        </select>
        <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Aplicar filtros</button>
    </form>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Titulo</th>
                    <th class="px-4 py-3 text-left">Parcela</th>
                    <th class="px-4 py-3 text-left">Categoria</th>
                    <th class="px-4 py-3 text-right">Valor</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($despesas as $despesa)
                    <tr>
                        <td class="px-4 py-3">{{ $despesa->data->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $despesa->titulo }}</td>
                        <td class="px-4 py-3">
                            @if ($despesa->total_parcelas)
                                {{ $despesa->parcela_atual }}/{{ $despesa->total_parcelas }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $despesa->categoria?->nome ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-700">R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('despesas.edit', $despesa) }}" class="rounded border border-gray-300 px-3 py-1.5 text-xs hover:bg-gray-50">Editar</a>
                                <form method="POST" action="{{ route('despesas.destroy', $despesa) }}" onsubmit="return confirm('Deseja excluir esta despesa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded border border-red-300 px-3 py-1.5 text-xs text-red-700 hover:bg-red-50">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">Nenhuma despesa encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $despesas->links() }}</div>
</x-app-layout>
