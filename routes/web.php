<?php

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

use Illuminate\Support\Facades\Route;

Route::get('{path}', function () {

    $currentHost = "http://{$_SERVER['HTTP_HOST']}";

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $currentHost = str_replace('http://', 'https://', $currentHost);
    }

    return view('index', [
        'currentHost' => $currentHost
    ]);
})->where('path', '(.*)');
