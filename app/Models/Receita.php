<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receita extends Model
{
    protected $fillable = [
        'user_id',
        'categoria_id',
        'lucro_fixo_id',
        'titulo',
        'valor',
        'data',
        'descricao',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'valor' => 'decimal:2',
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

    public function lucroFixo(): BelongsTo
    {
        return $this->belongsTo(LucroFixo::class);
    }
}
