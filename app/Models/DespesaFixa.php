<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DespesaFixa extends Model
{
    public const PERIODICIDADE_MENSAL = 'mensal';
    public const PERIODICIDADE_ANUAL = 'anual';

    protected $fillable = [
        'user_id',
        'categoria_id',
        'titulo',
        'valor',
        'dia_vencimento',
        'periodicidade',
        'renovacao_mes',
        'renovacao_ano',
        'descricao',
        'ativa',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'dia_vencimento' => 'integer',
            'renovacao_mes' => 'integer',
            'renovacao_ano' => 'integer',
            'ativa' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function despesasGeradas(): HasMany
    {
        return $this->hasMany(Despesa::class);
    }

    public function isAnual(): bool
    {
        return $this->periodicidade === self::PERIODICIDADE_ANUAL;
    }
}
