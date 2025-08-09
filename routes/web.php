<?php

use App\Http\Controllers\BonAchatController;
use App\Http\Controllers\BonVenteController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DepotAssignmentController;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ProduitController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::resource('fournisseurs', FournisseurController::class)->middleware(['auth', 'verified']);

Route::resource('clients', ClientController::class)->middleware(['auth', 'verified']);

Route::resource('categories', CategorieController::class)
    ->middleware(['auth', 'verified']);

Route::resource('produits', ProduitController::class)->middleware(['auth', 'verified']);

Route::resource('depots', DepotController::class)->middleware(['auth', 'verified']);

Route::resource('bon-achats', BonAchatController::class)->middleware(['auth', 'verified']);


Route::resource('bon-ventes', BonVenteController::class)->middleware(['auth', 'verified']);


Route::get('/bon-achats/{bonAchat}/assign/{produit}', [DepotAssignmentController::class, 'create'])
    ->name('depot-assignments.create');
Route::post('/bon-achats/{bonAchat}/assign-depot', [DepotAssignmentController::class, 'store'])
    ->name('depot-assignments.store');

Route::get('/bon-achats/{bonAchat}/assign/{produit}/edit', [DepotAssignmentController::class, 'edit'])
    ->name('depot-assignments.edit');

Route::put('/bon-achats/{bonAchat}/assign/{produit}', [DepotAssignmentController::class, 'update'])
    ->name('depot-assignments.update');



Route::get('/bon-achats/{n_ba}/export-pdf', [ExportController::class, 'exportBonAchatToPdf'])
    ->name('bon-achats.export-pdf');

Route::get('/bon-ventes/{n_bv}/export-pdf', [ExportController::class, 'exportBonVenteToPdf'])
    ->name('bon-ventes.export-pdf');


Route::get('/export-pdf', [ExportController::class, 'exportProduitsToPdf']);


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
