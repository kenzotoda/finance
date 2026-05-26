<?php

namespace App\Services\FaturaImport;

use App\Models\Cartao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FaturaStorageService
{
    public function storePreview(User $user, UploadedFile $arquivo): string
    {
        $path = sprintf(
            'previews/%d/%s_%s',
            $user->id,
            (string) Str::uuid(),
            $this->sanitizeFilename($arquivo->getClientOriginalName())
        );

        $stored = $this->disk()->put($path, $arquivo->getContent());

        if ($stored === false) {
            throw new RuntimeException('Nao foi possivel salvar a fatura no bucket Supabase S3.');
        }

        return $path;
    }

    public function finalizeFatura(User $user, Cartao $cartao, Carbon $competencia, string $previewPath, string $arquivoNome): string
    {
        $disk = $this->disk();
        $finalPath = sprintf(
            'faturas/%d/cartao-%d/%s/%s',
            $user->id,
            $cartao->id,
            $competencia->format('Y-m'),
            $this->sanitizeFilename($arquivoNome)
        );

        if (! $disk->exists($previewPath)) {
            throw new RuntimeException('Arquivo da previa nao encontrado no bucket Supabase S3.');
        }

        if ($previewPath === $finalPath) {
            return $finalPath;
        }

        if ($disk->exists($finalPath)) {
            $disk->delete($finalPath);
        }

        $this->relocateFile($disk, $previewPath, $finalPath);

        return $finalPath;
    }

    /**
     * Supabase S3 nao suporta GetObjectAcl; move()/copy() do Flysystem falham ao ler visibility.
     */
    private function relocateFile(Filesystem $disk, string $from, string $to): void
    {
        $content = $disk->get($from);

        if ($content === null || $content === false) {
            throw new RuntimeException('Nao foi possivel ler a fatura no bucket Supabase S3.');
        }

        if (! $disk->put($to, $content)) {
            throw new RuntimeException('Nao foi possivel salvar a fatura no destino final no bucket.');
        }

        $disk->delete($from);
    }

    public function delete(string $path): void
    {
        if ($path === '') {
            return;
        }

        $this->disk()->delete($path);
    }

    private function disk(): Filesystem
    {
        return Storage::disk('supabase');
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = trim($filename);
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '_', $filename) ?? 'fatura';

        return $filename !== '' ? $filename : 'fatura';
    }
}
