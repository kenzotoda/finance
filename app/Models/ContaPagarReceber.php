<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContaPagarReceber extends Model
{
    public const TIPO_PAGAR = 'pagar';

    public const TIPO_RECEBER = 'receber';

    protected $table = 'contas_pagar_receber';

    protected $fillable = [
        'user_id',
        'categoria_id',
        'tipo',
        'titulo',
        'descricao',
        'valor',
        'data',
        'grupo_parcelamento_id',
        'parcela_atual',
        'total_parcelas',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data' => 'date',
            'parcela_atual' => 'integer',
            'total_parcelas' => 'integer',
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

    public function isParcelada(): bool
    {
        return $this->grupo_parcelamento_id !== null
            && $this->total_parcelas !== null
            && $this->total_parcelas > 1;
    }
}
