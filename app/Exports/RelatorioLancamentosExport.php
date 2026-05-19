<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RelatorioLancamentosExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $lancamentos) {}

    public function collection(): Collection
    {
        return $this->lancamentos;
    }

    public function headings(): array
    {
        return ['Tipo', 'Data', 'Titulo', 'Categoria', 'Valor'];
    }

    public function map($row): array
    {
        return [
            $row['tipo'],
            $row['data'],
            $row['titulo'],
            $row['categoria'] ?: '-',
            number_format((float) $row['valor'], 2, '.', ''),
        ];
    }
}
