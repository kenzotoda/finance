<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receitas(): HasMany
    {
        return $this->hasMany(Receita::class);
    }

    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class);
    }

    public function despesasFixas(): HasMany
    {
        return $this->hasMany(DespesaFixa::class);
    }

    public function openFinanceTransactions(): HasMany
    {
        return $this->hasMany(OpenFinanceTransaction::class);
    }
}
