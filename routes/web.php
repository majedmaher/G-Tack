<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/backup', function() {
    $file = Carbon::now()->format('Y-m-d-H-i-s') . '-mybackup.sql';
    dd(Artisan::call('db:backup', [
        'file' => $file,
    ]));

    //dd(Artisan::call('db:restore 2023-05-20-09-29-23.sql'));
    // $config = config('database.connections.mysql');
    //     $filename = storage_path('app/backups/' . Carbon::now()->format('Y-m-d-H-i-s') . '.sql');

    //     $command = "mysqldump -u {$config['username']} {$config['database']} > {$filename}";
    //     exec($command);
    //     dd($filename);
});
