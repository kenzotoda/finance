<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaturaCartao extends Model
{
    protected $table = 'fatura_cartoes';

    protected $fillable = [
        'user_id',
        'cartao_id',
        'competencia',
        'arquivo_nome',
        'arquivo_hash',
        'arquivo_path',
        'total_lancamentos',
        'total_valor',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'competencia' => 'date',
            'total_lancamentos' => 'integer',
            'total_valor' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartao(): BelongsTo
    {
        return $this->belongsTo(Cartao::class);
    }

    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class);
    }
}
