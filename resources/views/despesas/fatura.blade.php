<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800">
                Fatura {{ $fatura->competencia->format('m/Y') }} - {{ $fatura->cartao->nome }}
            </h2>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('despesas.index', ['cartao_id' => $fatura->cartao_id]) }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                    Voltar
                </a>
                <form method="POST" action="{{ route('despesas.faturas.destroy', $fatura) }}" onsubmit="return confirm('Excluir esta fatura? Todos os lancamentos serao removidos permanentemente.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-700 hover:bg-red-50">
                        Excluir fatura
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="mb-4 rounded-xl bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-700">
            Arquivo: <span class="font-medium">{{ $fatura->arquivo_nome }}</span> |
            Lancamentos: <span class="font-medium">{{ $fatura->despesas()->count() }}</span> |
            Total: <span class="font-medium text-red-700">R$ {{ number_format((float) $fatura->despesas()->sum('valor'), 2, ',', '.') }}</span>
        </p>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Titulo</th>
                    <th class="px-4 py-3 text-right">Valor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($despesas as $despesa)
                    <tr>
                        <td class="px-4 py-3">{{ $despesa->data->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $despesa->titulo }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-700">
                            R$ {{ number_format((float) $despesa->valor, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">Nenhum lancamento nesta fatura.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $despesas->links() }}</div>
</x-app-layout>
