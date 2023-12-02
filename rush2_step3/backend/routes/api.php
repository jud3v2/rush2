<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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


Route::get("/tar/{name}", function(Request $request, string $name) {
    return $name;
})->name('get_tarball');

Route::get("/tar/download", function(Request $request, string $name) {
    return $name;
})->name('download_tarball');


Route::post('/tar/files/upload', function(Request $request) {
    return 'output.mytar';
})->name('post_files');

Route::post('/tar/generate', function(Request $request) {
    return 'output.mytar';
})->name('generate_tarball');

Route::get('/ping', function() {
    return response()->json(["message" => 'ok']);
});
