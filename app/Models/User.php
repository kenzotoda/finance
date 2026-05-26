<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'receita_liquida_mensal'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'receita_liquida_mensal' => 'decimal:2',
        ];
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
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

    public function lucrosFixos(): HasMany
    {
        return $this->hasMany(LucroFixo::class);
    }

    public function impostos(): HasMany
    {
        return $this->hasMany(Imposto::class);
    }

    public function cartoes(): HasMany
    {
        return $this->hasMany(Cartao::class);
    }

    public function faturasCartao(): HasMany
    {
        return $this->hasMany(FaturaCartao::class);
    }

    public function contasPagarReceber(): HasMany
    {
        return $this->hasMany(ContaPagarReceber::class);
    }
}
