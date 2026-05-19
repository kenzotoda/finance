<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenFinanceTransaction extends Model
{
    protected $fillable = [
        'open_finance_account_id',
        'user_id',
        'categoria_id',
        'pluggy_transaction_id',
        'description',
        'amount',
        'currency_code',
        'type',
        'status',
        'transaction_date',
        'auto_categorized',
        'synced_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'datetime',
            'auto_categorized' => 'boolean',
            'synced_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(OpenFinanceAccount::class, 'open_finance_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
