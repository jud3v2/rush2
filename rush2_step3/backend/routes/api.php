<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post("/tar/make", [FileController::class, 'store'])->name('get_tarball');

Route::get("/tar/test", [FileController::class, 'test'])->name('test_tarball');

Route::get("/tar/download", [FileController::class, "download"])->name('download_tarball');

Route::get('/ping', function() {
    return response()->json(["message" => 'ok']);
});
