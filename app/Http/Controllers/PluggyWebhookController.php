<?php

namespace App\Http\Controllers;

use App\Jobs\SyncOpenFinanceItemJob;
use App\Models\OpenFinanceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PluggyWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = (string) data_get($payload, 'event');
        $itemId = (string) data_get($payload, 'itemId', data_get($payload, 'item.id', ''));

        if ($itemId !== '' && in_array($event, ['item/created', 'item/updated', 'item/error'], true)) {
            $item = OpenFinanceItem::where('pluggy_item_id', $itemId)->first();

            if ($item instanceof OpenFinanceItem) {
                SyncOpenFinanceItemJob::dispatch($item->id);
            }
        }

        return response()->json(['received' => true]);
    }
}
