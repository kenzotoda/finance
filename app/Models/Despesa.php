<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despesa extends Model
{
    protected $fillable = [
        'user_id',
        'categoria_id',
        'despesa_fixa_id',
        'imposto_id',
        'compra_parcelada_id',
        'parcela_atual',
        'total_parcelas',
        'titulo',
        'valor',
        'data',
        'tipo',
        'descricao',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'valor' => 'decimal:2',
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

    public function despesaFixa(): BelongsTo
    {
        return $this->belongsTo(DespesaFixa::class);
    }

    public function imposto(): BelongsTo
    {
        return $this->belongsTo(Imposto::class);
    }
}
