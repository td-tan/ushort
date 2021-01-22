<?php declare(strict_types = 1);

require('../vendor/autoload.php');
require('../src/Helper/Route.php');


use Symfony\Component\Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use eftec\bladeone\BladeOne;

use App\Controller\HomeController;
use App\Controller\ApiController;

use App\Helper\Route;

// TODO Auth JWT library
// TODO Connection Postgresql pdo
// TODO Implement url redirection
// TODO Write unit tests

// Load from .env
$dotenv = new Dotenv();
$dotenv->load(dirname(__DIR__).'/.env');

// Load DB Connection
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER'],
    'host'      => $_ENV['DB_HOST'],
    'port'      => $_ENV['DB_PORT'],
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

// No direct access to ressources
if (preg_match('/\.(?:css|js|png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) 
{
    return false;
}

Route::Get('/', HomeController::class.'@index');

Route::Group('/api', function () {
    Route::Post('/login', ApiController::class.'@login');
    Route::Get('/user', ApiController::class.'@get_user');
    Route::Post('/refresh', ApiController::class.'@refresh_token');
});

// To render view
function view(string $viewname, array $viewargs) {
    $views = dirname(__DIR__) . '/src/View';
    $cache = dirname(__DIR__) . '/storage/cache';
    $blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

    return $blade->run($viewname, $viewargs);
}