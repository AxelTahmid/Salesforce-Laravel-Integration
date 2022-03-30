<?php

use App\Http\Controllers\SalesForceCustom;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () use ($router) {
//     return "Welcome to Salesforce Test";
// });

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('sf')->group(function () {
    Route::get('mc-token', [SalesForceCustom::class, 'sfmcToken']);
    Route::get('token', [SalesForceCustom::class, 'getToken']);
    Route::get('addTestUser', [SalesForceCustom::class, 'test']);
});
