<?php

namespace App\Services\FaturaImport;

use App\Models\Cartao;
use App\Models\FaturaCartao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FaturaImportService
{
    public function __construct(
        private readonly DespesaCategorizationService $categorizationService,
        private readonly FaturaStorageService $faturaStorageService,
    ) {}

    /**
     * @return array{
     *   arquivo_nome:string,
     *   arquivo_hash:string,
     *   cartao_id:int,
     *   cartao_nome:string,
     *   competencia:string,
     *   total_linhas:int,
     *   prontas_importacao:int,
     *   ignoradas:int,
     *   periodo_referencia:?string,
     *   linhas:array<int, array{
     *      indice:int,
     *      data:?string,
     *      titulo:string,
     *      valor:?float,
     *      tipo:string,
     *      status:string,
     *      motivo:?string,
     *      categoria_id:?int,
     *      hash_lancamento:?string,
     *      fatura_arquivo:string,
     *      fatura_hash:string
     *   }>
     * }
     */
    public function analyze(User $user, UploadedFile $arquivo, Cartao $cartao, Carbon $competencia): array
    {
        $conteudoArquivo = (string) $arquivo->getContent();
        $extensao = Str::lower((string) $arquivo->getClientOriginalExtension());
        $linhas = in_array($extensao, ['ofx'], true)
            ? $this->parseOfx($conteudoArquivo)
            : $this->parseCsv($conteudoArquivo);
        $faturaHash = hash('sha256', $conteudoArquivo);

        $prontasImportacao = 0;
        $ignoradas = 0;
        $linhasAnalisadas = [];

        foreach ($linhas as $index => $linha) {
            $data = $this->parseDate((string) data_get($linha, 'data'));
            $titulo = trim((string) data_get($linha, 'titulo'));
            $valorOriginal = $this->parseAmount((string) data_get($linha, 'valor'));
            $tipo = Str::lower((string) data_get($linha, 'tipo', 'debit'));
            $motivo = null;
            $categoriaId = null;
            $status = 'pronta';
            $hashLancamento = null;

            if (! $data || $titulo === '' || $valorOriginal === null) {
                $status = 'ignorada';
                $motivo = 'Linha invalida (data/titulo/valor).';
                $ignoradas++;
            }

            if ($status === 'pronta' && $this->isPagamentoOuCredito($titulo, $valorOriginal, $tipo)) {
                $status = 'ignorada';
                $motivo = 'Pagamento ou lancamento de credito.';
                $ignoradas++;
            }

            $valor = $valorOriginal !== null ? abs($valorOriginal) : null;
            if ($status === 'pronta' && ($valor === null || $valor <= 0)) {
                $status = 'ignorada';
                $motivo = 'Valor invalido para despesa.';
                $ignoradas++;
            }

            if ($status === 'pronta') {
                $categoriaId = $this->categorizationService->resolveCategoriaId($user, $titulo);
                $hashLancamento = $this->makeHashLancamento($data, $titulo, $valor, $index + 1);
            }

            if ($status === 'pronta') {
                $prontasImportacao++;
            }

            $linhasAnalisadas[] = [
                'indice' => $index + 1,
                'data' => $data?->toDateString(),
                'titulo' => $titulo,
                'valor' => $valor,
                'tipo' => $tipo,
                'status' => $status,
                'motivo' => $motivo,
                'categoria_id' => $categoriaId,
                'hash_lancamento' => $hashLancamento,
                'fatura_arquivo' => $arquivo->getClientOriginalName(),
                'fatura_hash' => $faturaHash,
            ];
        }

        $datas = collect($linhasAnalisadas)
            ->pluck('data')
            ->filter()
            ->sort()
            ->values();

        return [
            'arquivo_nome' => $arquivo->getClientOriginalName(),
            'arquivo_hash' => $faturaHash,
            'cartao_id' => $cartao->id,
            'cartao_nome' => $cartao->nome,
            'competencia' => $competencia->toDateString(),
            'total_linhas' => count($linhasAnalisadas),
            'prontas_importacao' => $prontasImportacao,
            'ignoradas' => $ignoradas,
            'periodo_referencia' => $this->resolvePeriodoReferencia($datas),
            'linhas' => $linhasAnalisadas,
        ];
    }

    /**
     * @param array{
     *   cartao_id:int,
     *   competencia:string,
     *   arquivo_nome:string,
     *   arquivo_hash:string,
     *   arquivo_path:?string,
     *   linhas:array<int, array{
     *      data:?string,
     *      titulo:string,
     *      valor:?float,
     *      status:string,
     *      categoria_id:?int,
     *      hash_lancamento:?string,
     *      fatura_arquivo:?string,
     *      fatura_hash:?string
     *   }>
     * } $preview
     * @return array{importadas:int, ignoradas:int}
     */
    public function importPreview(User $user, array $preview): array
    {
        $cartao = $user->cartoes()->findOrFail((int) $preview['cartao_id']);
        $competencia = Carbon::parse((string) $preview['competencia'])->startOfMonth();
        $arquivoNome = (string) ($preview['arquivo_nome'] ?? 'fatura');
        $previewPath = (string) ($preview['arquivo_path'] ?? '');

        $arquivoPath = $previewPath !== ''
            ? $this->faturaStorageService->finalizeFatura($user, $cartao, $competencia, $previewPath, $arquivoNome)
            : null;

        $fatura = FaturaCartao::create([
            'user_id' => $user->id,
            'cartao_id' => $cartao->id,
            'competencia' => $competencia->toDateString(),
            'arquivo_nome' => $arquivoNome,
            'arquivo_hash' => (string) ($preview['arquivo_hash'] ?? ''),
            'arquivo_path' => $arquivoPath,
            'status' => 'importada',
        ]);

        $resultado = $this->importFromPreview($user, $preview['linhas'] ?? [], $cartao->id, $fatura->id);

        $fatura->update([
            'total_lancamentos' => $resultado['importadas'],
            'total_valor' => $user->despesas()
                ->where('fatura_cartao_id', $fatura->id)
                ->sum('valor'),
        ]);

        return $resultado;
    }

    /**
     * @param  array<int, array{
     *   data:?string,
     *   titulo:string,
     *   valor:?float,
     *   status:string,
     *   categoria_id:?int,
     *   hash_lancamento:?string,
     *   fatura_arquivo:?string,
     *   fatura_hash:?string
     * }>  $linhas
     * @return array{importadas:int, ignoradas:int}
     */
    public function importFromPreview(User $user, array $linhas, ?int $cartaoId = null, ?int $faturaId = null): array
    {
        $importadas = 0;
        $ignoradas = 0;

        foreach ($linhas as $linha) {
            if (($linha['status'] ?? 'ignorada') !== 'pronta') {
                $ignoradas++;
                continue;
            }

            $data = isset($linha['data']) ? (string) $linha['data'] : '';
            $titulo = trim((string) ($linha['titulo'] ?? ''));
            $valor = (float) ($linha['valor'] ?? 0);
            $hashLancamento = (string) ($linha['hash_lancamento'] ?? '');
            $faturaArquivo = (string) ($linha['fatura_arquivo'] ?? '');
            $faturaHash = (string) ($linha['fatura_hash'] ?? '');

            if ($data === '' || $titulo === '' || $valor <= 0) {
                $ignoradas++;
                continue;
            }

            if ($hashLancamento === '') {
                $hashLancamento = $this->makeHashLancamento(Carbon::parse($data), $titulo, $valor, 0);
            }

            $user->despesas()->create([
                'categoria_id' => $linha['categoria_id'] ?? null,
                'despesa_fixa_id' => null,
                'imposto_id' => null,
                'cartao_id' => $cartaoId,
                'fatura_cartao_id' => $faturaId,
                'compra_parcelada_id' => null,
                'parcela_atual' => null,
                'total_parcelas' => null,
                'titulo' => $titulo,
                'valor' => $valor,
                'data' => $data,
                'tipo' => 'variavel',
                'origem' => 'fatura',
                'fatura_arquivo' => $faturaArquivo !== '' ? $faturaArquivo : null,
                'fatura_hash' => $faturaHash !== '' ? $faturaHash : null,
                'hash_lancamento' => $hashLancamento,
                'descricao' => $faturaArquivo !== '' ? "Importado de fatura: {$faturaArquivo}" : 'Importado de fatura',
            ]);

            $importadas++;
        }

        return ['importadas' => $importadas, 'ignoradas' => $ignoradas];
    }

    /**
     * @return array<int, array{data:string,titulo:string,valor:string,tipo:string}>
     */
    private function parseCsv(string $content): array
    {
        $content = trim($content);
        if ($content === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $content) ?: [];
        if (count($lines) < 2) {
            return [];
        }

        $delimiter = $this->detectDelimiter($lines[0]);
        $headers = str_getcsv((string) array_shift($lines), $delimiter);
        $headers = array_map(fn ($header) => Str::lower(Str::snake(Str::ascii((string) $header))), $headers);

        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $cols = str_getcsv($line, $delimiter);
            $item = [];
            foreach ($headers as $index => $header) {
                $item[$header] = $cols[$index] ?? null;
            }

            $rows[] = [
                'data' => (string) ($item['data'] ?? $item['date'] ?? $item['transaction_date'] ?? ''),
                'titulo' => (string) ($item['descricao'] ?? $item['description'] ?? $item['historico'] ?? $item['titulo'] ?? ''),
                'valor' => (string) ($item['valor'] ?? $item['amount'] ?? $item['value'] ?? ''),
                'tipo' => (string) ($item['tipo'] ?? $item['type'] ?? 'debit'),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{data:string,titulo:string,valor:string,tipo:string}>
     */
    private function parseOfx(string $content): array
    {
        $normalized = str_replace("\r", '', $content);
        $chunks = preg_split('/<STMTTRN>/i', $normalized) ?: [];
        array_shift($chunks);

        $rows = [];
        foreach ($chunks as $chunk) {
            $dtPosted = $this->extractOfxTagValue($chunk, 'DTPOSTED');
            $memo = $this->extractOfxTagValue($chunk, 'MEMO') ?? $this->extractOfxTagValue($chunk, 'NAME');
            $amount = $this->extractOfxTagValue($chunk, 'TRNAMT');
            $trnType = $this->extractOfxTagValue($chunk, 'TRNTYPE');

            $rows[] = [
                'data' => substr((string) $dtPosted, 0, 8),
                'titulo' => (string) $memo,
                'valor' => (string) $amount,
                'tipo' => (string) ($trnType ?: 'debit'),
            ];
        }

        return $rows;
    }

    private function extractOfxTagValue(string $chunk, string $tag): ?string
    {
        if (preg_match('/<' . preg_quote($tag, '/') . '>([^<\n]+)/i', $chunk, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }

    private function detectDelimiter(string $headerLine): string
    {
        $semicolonCount = substr_count($headerLine, ';');
        $commaCount = substr_count($headerLine, ',');

        return $semicolonCount > $commaCount ? ';' : ',';
    }

    private function parseDate(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Ymd'];
        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date !== false) {
                    return $date->startOfDay();
                }
            } catch (\Throwable) {
                // tenta proximo formato
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseAmount(string $value): ?float
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $normalized = str_replace(['R$', ' '], '', $value);

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            if (strrpos($normalized, ',') > strrpos($normalized, '.')) {
                $normalized = str_replace('.', '', $normalized);
                $normalized = str_replace(',', '.', $normalized);
            } else {
                $normalized = str_replace(',', '', $normalized);
            }
        } elseif (str_contains($normalized, ',')) {
            $normalized = str_replace(',', '.', $normalized);
        }

        if (! is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    private function makeHashLancamento(Carbon $data, string $titulo, float $valor, int $indice = 0): string
    {
        $tituloNormalizado = Str::of(Str::lower($titulo))
            ->ascii()
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->value();

        return hash('sha256', implode('|', [
            $data->toDateString(),
            $tituloNormalizado,
            number_format(abs($valor), 2, '.', ''),
            $indice,
        ]));
    }

    private function isPagamentoOuCredito(string $titulo, ?float $valor, string $tipo): bool
    {
        $tipo = Str::lower(trim($tipo));

        if (in_array($tipo, ['credit', 'credito', 'payment', 'pay', 'dep', 'deposit'], true)) {
            return true;
        }

        if ($valor !== null && $valor < 0) {
            if (in_array($tipo, ['debit', 'debito', 'd', 'expense', 'other'], true)) {
                return false;
            }

            return true;
        }

        $tituloNormalizado = Str::of(Str::lower($titulo))
            ->ascii()
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->value();

        foreach ([
            'pagamento recebido',
            'pagamento de fatura',
            'pagamento fatura',
            'pagamento efetuado',
            'pagamento da fatura',
            'credito em confianca',
            'estorno',
            'devolucao',
            'payment received',
        ] as $palavraChave) {
            if (str_contains($tituloNormalizado, Str::ascii($palavraChave))) {
                return true;
            }
        }

        return false;
    }

    private function resolvePeriodoReferencia(Collection $datas): ?string
    {
        if ($datas->isEmpty()) {
            return null;
        }

        $inicio = Carbon::parse((string) $datas->first())->format('d/m/Y');
        $fim = Carbon::parse((string) $datas->last())->format('d/m/Y');

        return $inicio === $fim ? $inicio : "{$inicio} ate {$fim}";
    }
}
