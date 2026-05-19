<?php

namespace App\Jobs;

use App\Models\OpenFinanceItem;
use App\Services\OpenFinance\SyncOpenFinanceItemService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncOpenFinanceItemJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $openFinanceItemId) {}

    public function handle(SyncOpenFinanceItemService $syncService): void
    {
        $item = OpenFinanceItem::find($this->openFinanceItemId);

        if (! $item instanceof OpenFinanceItem) {
            return;
        }

        $syncService->execute($item);
    }
}
