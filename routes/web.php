<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\DespesaFixaController;
use App\Http\Controllers\ImpostoController;
use App\Http\Controllers\LucroFixoController;
use App\Http\Controllers\OpenFinanceController;
use App\Http\Controllers\PluggyWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? to_route('dashboard') : to_route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('despesas', DespesaController::class);
    Route::resource('despesas-fixas', DespesaFixaController::class)
        ->parameters(['despesas-fixas' => 'despesaFixa']);
    Route::resource('lucros-fixos', LucroFixoController::class)
        ->parameters(['lucros-fixos' => 'lucroFixo']);
    Route::resource('impostos', ImpostoController::class);
    Route::get('/open-finance', [OpenFinanceController::class, 'index'])->name('open-finance.index');
    Route::post('/open-finance/connect-token', [OpenFinanceController::class, 'connectToken'])->name('open-finance.connect-token');
    Route::post('/open-finance/items', [OpenFinanceController::class, 'storeItem'])->name('open-finance.items.store');
    Route::post('/open-finance/items/{openFinanceItem}/sync', [OpenFinanceController::class, 'sync'])->name('open-finance.items.sync');
    Route::resource('categorias', CategoriaController::class);
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/export/excel', [RelatorioController::class, 'exportExcel'])->name('relatorios.export.excel');
    Route::get('/relatorios/export/pdf', [RelatorioController::class, 'exportPdf'])->name('relatorios.export.pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/webhooks/pluggy', PluggyWebhookController::class)->name('webhooks.pluggy');

require __DIR__.'/auth.php';
