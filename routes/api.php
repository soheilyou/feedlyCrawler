<?php

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

Route::get('/test', function (Request $request) {
    $c = new \App\Classes\Crawler();
    dd($c->getNewItems('https://www.digikala.com/mag/feed/', \Carbon\Carbon::parse('Thu, 01 Sep 2022 14:42:03 +0000')));
    return;
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// TODO :: service middleware
Route::post('/feed', [\App\Http\Controllers\Services\FeedController::class, 'store']);
