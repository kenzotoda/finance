<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatorio Financeiro</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        .meta { margin-bottom: 16px; }
        .cards { margin-bottom: 16px; }
        .card { display: inline-block; width: 31%; padding: 8px; border: 1px solid #d1d5db; margin-right: 1%; vertical-align: top; }
        .label { font-size: 10px; color: #6b7280; }
        .value { font-size: 16px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Relatorio Financeiro</h1>
    <div class="meta">
        Competencia: {{ $competenciaLabel }}
    </div>

    <div class="cards">
        <div class="card">
            <div class="label">Total receitas</div>
            <div class="value">R$ {{ number_format($totalReceitas, 2, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="label">Total despesas</div>
            <div class="value">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="label">Saldo</div>
            <div class="value">R$ {{ number_format($saldo, 2, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Data</th>
                <th>Titulo</th>
                <th>Categoria</th>
                <th class="right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lancamentos as $lancamento)
                <tr>
                    <td>{{ $lancamento['tipo'] }}</td>
                    <td>{{ $lancamento['data'] }}</td>
                    <td>{{ $lancamento['titulo'] }}</td>
                    <td>{{ $lancamento['categoria'] ?: '-' }}</td>
                    <td class="right">R$ {{ number_format($lancamento['valor'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhum lancamento encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
