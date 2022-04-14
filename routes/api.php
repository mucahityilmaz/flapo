<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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

// So all routes are wrapped under /api and has a middleware for url parameter validation
// defined in app/Http/Middleware/EnsureInputUrlIsValid.php and app/Http/Kernel.php

Route::get('/cheapAndExpensive', [ProductController::class, 'cheapAndExpensive']);

Route::get('/byPrice', [ProductController::class, 'byPrice']);

Route::get('/mostBottles', [ProductController::class, 'mostBottles']);

Route::get('/all', [ProductController::class, 'all']);