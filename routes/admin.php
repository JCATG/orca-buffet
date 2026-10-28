<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('categories', 'pages::admin.categories.index')->name('categories.index');

    Route::livewire('orcamentos', 'pages::admin.orcamento.index')->name('orcamento.index');
    Route::livewire('orcamentos/{order}', 'pages::admin.orcamento.show')->name('orcamento.show');

    Route::livewire('customers', 'pages::admin.customers.index')->name('customers.index');

    Route::livewire('products', 'pages::admin.products.index')->name('products.index');

    Route::livewire('users', 'pages::admin.users.index')->name('users.index');

    Route::livewire('configs', 'pages::admin.configs.index')->name('configs.index');
});