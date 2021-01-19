<?php declare(strict_types = 1);
session_start();

require('../vendor/autoload.php');
require('../src/Helper/Route.php');

use App\Controller\HomeController;

use Helper\Route;

// TODO Auth JWT library
// TODO Connection Postgresql pdo
// TODO Implement url redirection

Route::Get('/', HomeController::class.'@index');
Route::Get('/test/{id}', HomeController::class.'@index');
var_dump(Route::$route);
Route::Group('/auth', function () {
    Route::Get("/test/{id}", HomeController::class.'@index');
    var_dump(Route::$route);
});
var_dump(Route::$route);