<?php

use App\Models\Blog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if(Config::get("database.default") == "dynamic") {
        $posts = Blog::all();

        return $posts->toArray();
    }

    return view('welcome');
});

Route::get('/call-artisan/{command?}', function ($command = 'list') {

    // dd(DB::connection('dynamic'));

    $allowCommands = [
        "migrate:install",
        "migrate:status",
        "migrate",
        "key:generate",
        "storage:link",
        "route:cache",
        "route:clear",
        "view:cache",
        "view:clear",
        "cache:clear",
        "config:cache",
        "config:clear",
    ];

    if($command == 'list') {
        return $allowCommands;
    }

    if(!in_array($command, $allowCommands)) {
        return "Not Allow";
    }

    $parameters = [];

    if(Config::get("database.default") == "dynamic") {

        if($command == "migrate" || $command == "migrate:status") {
            $parameters["--path"] = "/database/migrations/clients";
        }
    }

    Artisan::call($command, $parameters);

    dd(Artisan::output());
});
