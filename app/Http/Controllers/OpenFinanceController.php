<?php

namespace App\Http\Controllers;

use App\Jobs\SyncOpenFinanceItemJob;
use App\Models\OpenFinanceItem;
use App\Services\OpenFinance\ConnectTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OpenFinanceController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $items = $user->openFinanceItems()
            ->with(['accounts', 'accounts.transactions' => fn ($query) => $query->latest('transaction_date')->limit(5)])
            ->latest()
            ->get();

        $accountsCount = $items->sum(fn ($item) => $item->accounts->count());
        $transactionsCount = $user->openFinanceTransactions()->count();
        $totalDebit = (float) $user->openFinanceTransactions()
            ->where('amount', '<', 0)
            ->sum('amount');
        $totalCredit = (float) $user->openFinanceTransactions()
            ->where('amount', '>=', 0)
            ->sum('amount');

        return view('open-finance.index', compact(
            'items',
            'accountsCount',
            'transactionsCount',
            'totalDebit',
            'totalCredit',
        ));
    }

    public function connectToken(ConnectTokenService $connectTokenService): JsonResponse
    {
        $token = $connectTokenService->generate(Auth::user());

        return response()->json(['accessToken' => $token]);
    }

    public function storeItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item_id' => ['required', 'string', 'max:100'],
        ]);

        $item = OpenFinanceItem::firstOrCreate(
            ['pluggy_item_id' => $data['item_id']],
            ['user_id' => Auth::id()]
        );

        if ($item->user_id !== Auth::id()) {
            abort(403);
        }

        SyncOpenFinanceItemJob::dispatch($item->id);

        return response()->json([
            'message' => 'Conta conectada. Sincronizacao em andamento.',
            'item_id' => $item->id,
        ], 201);
    }

    public function sync(OpenFinanceItem $openFinanceItem): RedirectResponse
    {
        abort_unless($openFinanceItem->user_id === Auth::id(), 403);

        SyncOpenFinanceItemJob::dispatch($openFinanceItem->id);

        return to_route('open-finance.index')
            ->with('success', 'Sincronizacao iniciada em fila.');
    }
}
