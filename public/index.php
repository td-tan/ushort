<?php declare(strict_types = 1);
session_start();

require('../vendor/autoload.php');
require('../src/Helper/Route.php');

use Symfony\Component\Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\Controller\HomeController;

use Helper\Route;

// TODO Auth JWT library
// TODO Connection Postgresql pdo
// TODO Implement url redirection
// TODO Write unit tests

// Load from .env
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER'],
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_NAME'],
    'username'  => $_ENV['DB_USER'],
    'password'  => $_ENV['DB_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();


// Routing

Route::Get('/', HomeController::class.'@index');
Route::Get('/test/{id}', HomeController::class.'@index');
Route::Put('/test/{t}', HomeController::class.'@index');

Route::Group('/auth', function () {
    Route::Get("/test/{id}", HomeController::class.'@index');
    Route::Post("/test/user/{user_id}", HomeController::class.'@index');
});