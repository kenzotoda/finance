<?php

namespace App\Services\OpenFinance;

use App\Models\OpenFinanceAccount;
use App\Models\OpenFinanceItem;
use App\Models\OpenFinanceTransaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SyncOpenFinanceItemService
{
    public function __construct(
        private readonly PluggyClient $pluggyClient,
        private readonly TransactionCategorizationService $categorizationService,
    ) {}

    public function execute(OpenFinanceItem $item): void
    {
        $remoteItem = $this->pluggyClient->fetchItem($item->pluggy_item_id);

        DB::transaction(function () use ($item, $remoteItem): void {
            $item->update([
                'connector_id' => data_get($remoteItem, 'connector.id'),
                'connector_name' => data_get($remoteItem, 'connector.name'),
                'status' => data_get($remoteItem, 'status'),
                'execution_status' => data_get($remoteItem, 'executionStatus'),
                'last_error_code' => data_get($remoteItem, 'error.code'),
                'last_error_message' => data_get($remoteItem, 'error.message'),
                'pluggy_updated_at' => data_get($remoteItem, 'updatedAt'),
                'last_synced_at' => now(),
                'raw_payload' => $remoteItem,
            ]);

            $accounts = $this->pluggyClient->fetchAccounts($item->pluggy_item_id);

            foreach ($accounts as $remoteAccount) {
                $account = OpenFinanceAccount::updateOrCreate(
                    ['pluggy_account_id' => (string) data_get($remoteAccount, 'id')],
                    [
                        'open_finance_item_id' => $item->id,
                        'name' => (string) data_get($remoteAccount, 'name', 'Conta'),
                        'type' => data_get($remoteAccount, 'type'),
                        'subtype' => data_get($remoteAccount, 'subtype'),
                        'currency_code' => data_get($remoteAccount, 'currencyCode'),
                        'balance' => data_get($remoteAccount, 'balance'),
                        'balance_updated_at' => data_get($remoteAccount, 'balanceDate'),
                        'raw_payload' => $remoteAccount,
                    ]
                );

                $this->syncTransactions($item, $account);
            }
        });
    }

    private function syncTransactions(OpenFinanceItem $item, OpenFinanceAccount $account): void
    {
        $remoteTransactions = $this->pluggyClient->fetchTransactions($account->pluggy_account_id);

        foreach ($remoteTransactions as $remoteTransaction) {
            $description = data_get($remoteTransaction, 'description');
            $amount = (float) data_get($remoteTransaction, 'amount', 0);
            $type = (string) data_get($remoteTransaction, 'type', '');
            $isExpense = $amount < 0 || in_array(strtolower($type), ['debit', 'debitcard'], true);
            $categoria = ['categoria_id' => null, 'auto_categorized' => false];

            if ($isExpense) {
                $categoria = $this->categorizationService->categorize($item->user, $description);
            }

            OpenFinanceTransaction::updateOrCreate(
                ['pluggy_transaction_id' => (string) data_get($remoteTransaction, 'id')],
                [
                    'open_finance_account_id' => $account->id,
                    'user_id' => $item->user_id,
                    'categoria_id' => Arr::get($categoria, 'categoria_id'),
                    'description' => $description,
                    'amount' => $amount,
                    'currency_code' => data_get($remoteTransaction, 'currencyCode'),
                    'type' => $type,
                    'status' => data_get($remoteTransaction, 'status'),
                    'transaction_date' => data_get($remoteTransaction, 'date'),
                    'auto_categorized' => Arr::get($categoria, 'auto_categorized', false),
                    'synced_at' => now(),
                    'raw_payload' => $remoteTransaction,
                ]
            );
        }
    }
}
