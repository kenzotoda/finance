<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenFinanceItem extends Model
{
    protected $fillable = [
        'user_id',
        'pluggy_item_id',
        'connector_id',
        'connector_name',
        'status',
        'execution_status',
        'last_error_code',
        'last_error_message',
        'pluggy_updated_at',
        'last_synced_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'pluggy_updated_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(OpenFinanceAccount::class);
    }
}
