<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Imposto extends Model
{
    public const PERIODICIDADE_MENSAL = 'mensal';

    public const PERIODICIDADE_ANUAL = 'anual';

    public const TIPO_IPTU = 'iptu';

    public const TIPO_IPVA = 'ipva';

    public const TIPO_IRPF = 'irpf';

    public const TIPO_ISS = 'iss';

    public const TIPO_INSS = 'inss';

    public const TIPO_OUTROS = 'outros';

    public const TIPOS = [
        self::TIPO_IPTU => 'IPTU',
        self::TIPO_IPVA => 'IPVA',
        self::TIPO_IRPF => 'IRPF',
        self::TIPO_ISS => 'ISS',
        self::TIPO_INSS => 'INSS',
        self::TIPO_OUTROS => 'Outros',
    ];

    protected $fillable = [
        'user_id',
        'categoria_id',
        'tipo',
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

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo);
    }

    public function isAnual(): bool
    {
        return $this->periodicidade === self::PERIODICIDADE_ANUAL;
    }
}
