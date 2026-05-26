<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\DespesaFixaController;
use App\Http\Controllers\ImpostoController;
use App\Http\Controllers\LucroFixoController;
use App\Http\Controllers\PagarReceberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? to_route('dashboard') : to_route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('despesas', DespesaController::class);
    Route::post('/despesas/cartoes', [DespesaController::class, 'storeCartao'])->name('despesas.cartoes.store');
    Route::delete('/despesas/cartoes/{cartao}', [DespesaController::class, 'destroyCartao'])->name('despesas.cartoes.destroy');
    Route::post('/despesas/importar-fatura', [DespesaController::class, 'importarFatura'])->name('despesas.importar-fatura');
    Route::post('/despesas/importar-fatura/confirmar', [DespesaController::class, 'confirmarImportacaoFatura'])->name('despesas.importar-fatura.confirmar');
    Route::post('/despesas/importar-fatura/cancelar', [DespesaController::class, 'cancelarImportacaoFatura'])->name('despesas.importar-fatura.cancelar');
    Route::get('/despesas/faturas/{faturaCartao}', [DespesaController::class, 'showFatura'])->name('despesas.faturas.show');
    Route::delete('/despesas/faturas/{faturaCartao}', [DespesaController::class, 'destroyFatura'])->name('despesas.faturas.destroy');
    Route::resource('despesas-fixas', DespesaFixaController::class)
        ->parameters(['despesas-fixas' => 'despesaFixa']);
    Route::resource('lucros-fixos', LucroFixoController::class)
        ->parameters(['lucros-fixos' => 'lucroFixo']);
    Route::resource('pagar-receber', PagarReceberController::class)
        ->parameters(['pagar-receber' => 'contaPagarReceber']);
    Route::delete('/pagar-receber/grupo/{grupoParcelamento}', [PagarReceberController::class, 'destroyGrupo'])
        ->name('pagar-receber.grupo.destroy');
    Route::delete('/pagar-receber/limpar/tudo', [PagarReceberController::class, 'limpar'])
        ->name('pagar-receber.limpar');
    Route::resource('impostos', ImpostoController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/export/excel', [RelatorioController::class, 'exportExcel'])->name('relatorios.export.excel');
    Route::get('/relatorios/export/pdf', [RelatorioController::class, 'exportPdf'])->name('relatorios.export.pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
