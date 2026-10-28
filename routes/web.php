<?php

use App\Http\Controllers\OrcamentoPublicoController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::frontend.home')->name('home');
Route::livewire('montar-orcamento', 'pages::frontend.orcamento')->name('orcamento.montar');

Route::get('orcamento/{order:token}', [OrcamentoPublicoController::class, 'show'])->name('orcamento.publico');
Route::get('orcamento/{order:token}/pdf', [OrcamentoPublicoController::class, 'pdf'])->name('orcamento.pdf');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});



require __DIR__.'/settings.php';
require __DIR__.'/admin.php';