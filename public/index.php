<?php declare(strict_types = 1);
session_start();

require('../vendor/autoload.php');
require('../src/Helper/Route.php');

use App\Controller\HomeController;

use Helper\Route;

// TODO Auth
// TODO Connection Postgresql pdo
// TODO Implement url redirection

Route::Get('/', HomeController::class.'@index');
Route::Get('/test/[a-zA-Z0-9]{4,128}', HomeController::class.'@index');
