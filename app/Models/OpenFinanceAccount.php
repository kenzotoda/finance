<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenFinanceAccount extends Model
{
    protected $fillable = [
        'open_finance_item_id',
        'pluggy_account_id',
        'name',
        'type',
        'subtype',
        'currency_code',
        'balance',
        'balance_updated_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'balance_updated_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OpenFinanceItem::class, 'open_finance_item_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(OpenFinanceTransaction::class);
    }
}
