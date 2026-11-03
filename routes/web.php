<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PesananLaundryController;

Route::get('/', function () {
    return redirect('/pesanan-laundry');
});

Route::get('/pesanan-laundry', [PesananLaundryController::class, 'index'])->name('laundry.index');
Route::post('/pesanan-laundry', [PesananLaundryController::class, 'store'])->name('laundry.store');
Route::get('/pesanan-laundry/{id}/edit', [PesananLaundryController::class, 'edit'])->name('laundry.edit');
Route::put('/pesanan-laundry/{id}', [PesananLaundryController::class, 'update'])->name('laundry.update');
Route::delete('/pesanan-laundry/{id}', [PesananLaundryController::class, 'destroy'])->name('laundry.destroy');