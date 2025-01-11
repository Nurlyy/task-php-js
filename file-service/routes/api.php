<?php

use App\Http\Controllers\FilesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("/files", [FilesController::class, 'index']);
Route::get("/files/{id}", [FilesController::class, 'getFile']);
Route::get("/suggestions", [FilesController::class, 'suggestions']);
Route::post('/files', [FileController::class, 'store']);
Route::put("/files/{id}", [FilesController::class, 'update']);
Route::delete("/files/{id}", [FilesController::class, 'delete']);

