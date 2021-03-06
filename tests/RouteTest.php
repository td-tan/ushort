<?php declare(strict_types=1);

use App\Helper\RequestData;
use App\Helper\Route;

use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{

    /**
     * @covers \App\Controller\Route::match
     */
    public function testCannotMatchStaticRoute(): void
    {
        // Fail Matches return null for Route::match
        self::assertNull(Route::match('/', '/home'));
        self::assertNull(Route::match('/home', '/'));
        self::assertNull(Route::match('/h', '/ho'));
        self::assertNull(Route::match('/ho', '/hom'));
        self::assertNull(Route::match('/hom', '/home'));
        self::assertNull(Route::match('/home', '/homee'));

        // Fail Match with slash at end of path
        self::assertNull(Route::match('/home', '/home/'));
        self::assertNull(Route::match('/user', '/user/'));
        
    }

    /**
     * @covers \App\Controller\Route::match
     */
    public function testCanMatchStaticRoute(): void
    {
        // Good Matches return [] for Route::match
        self::assertIsArray(Route::match('/', '/'));
        self::assertIsArray(Route::match('/home', '/home'));
        self::assertIsArray(Route::match('/user', '/user'));
        

        // Match long paths

        // Level 2 path
        self::assertIsArray(Route::match('/user/dashboard', '/user/dashboard'));
        self::assertIsArray(Route::match('/admin/dashboard', '/admin/dashboard'));
        // Level 3 path
        self::assertIsArray(Route::match('/user/user/user', '/user/user/user'));
        self::assertIsArray(Route::match('/admin/admin/admin', '/admin/admin/admin'));
    }

    /**
     * @covers \App\Controller\Route::match
     */
    public function testCannotMatchDynamicRoute(): void
    {
        // Rules declared {} for dynamic match
        self::assertNull(Route::match('/{id}', '/'));
        self::assertNull(Route::match('/{id}', '/id/'));
        self::assertNull(Route::match('/{id}', '/id/123'));
        self::assertNull(Route::match('/{id}', '/_'));
        self::assertNull(Route::match('/{id}', '/_123'));

        // Regex rules
        self::assertNull(Route::match('/[0-9]', '/a'));
        self::assertNull(Route::match('/[0-9]', '/ab'));
        // alpha
        self::assertNull(Route::match('/[a-z]', '/1'));
        self::assertNull(Route::match('/[a-z]', '/12'));
        // capital alpha
        self::assertNull(Route::match('/[A-Z]', '/a'));
        // digits with length constraints
        self::assertNull(Route::match('/[0-9]{4,}', '/123'));
        // Cannot use {1} because param_rules matching to query declaration
        self::assertNull(Route::match('/[0-9]{1,1}', '/12'));

        // Long path
        self::assertNull(Route::match('/user/{id}', '/'));
        self::assertNull(Route::match('/user/{id}', '/user'));
        self::assertNull(Route::match('/user/{id}', '/user/'));
        self::assertNull(Route::match('/user/{id}', '/user/_'));

        // Cannot match multiple declared queries
        self::assertNull(Route::match('/{product_name}/{id}', '/milk/0'));
        self::assertNull(Route::match('/{product_name}/{id}', '/milk/123'));

        // Regex rules in long path
        self::assertNull(Route::match('/[0-9]/[a-z]', '/a'));
    }

    /**
     * @covers \App\Controller\Route::match
     */
    public function testCanMatchDynamicRoute(): void
    {
        self::assertIsArray(Route::match('/{id}', '/123'));
        self::assertIsArray(Route::match('/{id}', '/abc'));

        self::assertIsArray(Route::match('/user/{id}', '/user/123'));

        self::assertIsArray(Route::match('/[0-9]', '/1'));
        self::assertIsArray(Route::match('/[0-9]/[a-z]', '/1/a'));
        self::assertIsArray(Route::match('/[0-9]{4,4}/[a-z]{2,2}', '/1234/ab'));

        self::assertIsArray(Route::match('/[0-9]+/[a-z]+/[0-9]+/[a-z]+', '/123/abcde/456789/fghijklmn'));
    }

    /**
     * @covers \App\Controller\Route::mapping
     * @covers \App\Controller\Route::match
     */
    public function testCannotMapRoute() : void
    {
        // Test invalid request method
        $_SERVER['REQUEST_METHOD'] = '';
        self::assertFalse(Route::mapping('GET', '/'));
        self::assertFalse(Route::mapping('POST', '/'));
        self::assertFalse(Route::mapping('PUT', '/'));
        self::assertFalse(Route::mapping('DELETE', '/'));

        // Test with valid request method but invalid uri
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '';
        self::assertFalse(Route::mapping('GET', '/'));
        // Test valid uri
        $_SERVER['REQUEST_URI'] = '/';
        self::assertFalse(Route::mapping('POST', '/'));
        self::assertFalse(Route::mapping('PUT', '/'));
        self::assertFalse(Route::mapping('DELETE', '/'));

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '';
        self::assertFalse(Route::mapping('POST', '/'));

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_SERVER['REQUEST_URI'] = '';
        self::assertFalse(Route::mapping('PUT', '/'));

        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['REQUEST_URI'] = '';
        self::assertFalse(Route::mapping('DELETE', '/'));
    }

    /**
     * @covers \App\Controller\Route::mapping
     * @covers \App\Controller\Route::match
     */
    public function testCanMapRoute(): void
    {
        // Test valid request method & uri path
        $_SERVER['REQUEST_URI'] = '/';
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) { 
            $_SERVER['REQUEST_METHOD'] = $method;
            $result = Route::mapping($method, '/');
            self::assertIsArray($result);
            // Array empty no query param
            self::assertEquals($result, []);
        }

        $_SERVER['REQUEST_METHOD'] = 'GET';
        self::assertIsArray(Route::mapping('GET', '/'));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        self::assertIsArray(Route::mapping('POST', '/'));
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        self::assertIsArray(Route::mapping('PUT', '/'));
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        self::assertIsArray(Route::mapping('DELETE', '/'));


        // Test valid method & path with query params
        $_SERVER['REQUEST_URI'] = '/123';
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) { 
            $_SERVER['REQUEST_METHOD'] = $method;
            $result = Route::mapping($method, '/{id}');
            self::assertIsArray($result);
            // Array empty no query param
            self::assertEquals($result, ['id' => '123']);
        }

        
    }

    /**
     * @covers \App\Controller\Route::loadController
     */
    public function testCannotLoadController() : void
    {
        // Controller not found
        Route::$controller_path = __DIR__."/";
        self::assertFalse(Route::loadController("ControllerDoesNotExists@action", new RequestData()));

        // Controller not found
        Route::$controller_path = __DIR__."/../Controller/";
        self::assertFalse(Route::loadController("HomeController@action_does_not_exists", new RequestData()));

        // Action not found
        Route::$controller_path = __DIR__."/../src/Controller/";
        $this->expectException(TypeError::class);
        self::assertFalse(Route::loadController("App\Controller\HomeController@action_does_not_exists", new RequestData()));
    }

    /**
     * @covers \App\Controller\Route::loadController
     */
    public function testCanLoadController() : void
    {
        $_ENV['DEBUG'] = true;
        // Controller & action found
        Route::$controller_path = __DIR__."/../src/Controller/";
        self::assertTrue(Route::loadController(App\Controller\HomeController::class."@index", new RequestData()));
    }

    /**
     * @covers \App\Controller\Route::mapping
     * @covers \App\Controller\Route::match
     * @covers \App\Controller\Route::loadController
     * @covers \App\Controller\Route::Get
     * @covers \App\Controller\Route::Post
     * @covers \App\Controller\Route::Put
     * @covers \App\Controller\Route::Delete
     */
    public function testCannotMatchRoute() : void
    {
        $_ENV['DEBUG'] = true;
        $_SERVER['REQUEST_URI'] = '/home';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Route::$controller_path = __DIR__."/../../src/Controller/";

        self::assertFalse(Route::Get('/', App\Controller\HomeController::class."@index"));

        $_SERVER['REQUEST_METHOD'] = 'POST';
        self::assertFalse(Route::Post('/', App\Controller\HomeController::class."@index"));

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        self::assertFalse(Route::Put('/', App\Controller\HomeController::class."@index"));

        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        self::assertFalse(Route::Delete('/', App\Controller\HomeController::class."@index"));
    }


    /**
     * @covers \App\Controller\Route::mapping
     * @covers \App\Controller\Route::match
     * @covers \App\Controller\Route::loadController
     * @covers \App\Controller\Route::Get
     * @covers \App\Controller\Route::Post
     * @covers \App\Controller\Route::Put
     * @covers \App\Controller\Route::Delete
     */
    public function testCanMatchRoute() : void
    {
        $_ENV['DEBUG'] = true;
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Route::$controller_path = __DIR__."/../../src/Controller/";

        self::assertTrue(Route::Get('/', App\Controller\HomeController::class."@index"));

        $_SERVER['REQUEST_METHOD'] = 'POST';
        self::assertTrue(Route::Post('/', App\Controller\HomeController::class."@index"));

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        self::assertTrue(Route::Put('/', App\Controller\HomeController::class."@index"));

        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        self::assertTrue(Route::Delete('/', App\Controller\HomeController::class."@index"));
    }

    /**
     * @covers \App\Controller\Route::mapping
     * @covers \App\Controller\Route::match
     * @covers \App\Controller\Route::loadController
     * @covers \App\Controller\Route::Get
     * @covers \App\Controller\Route::Post
     * @covers \App\Controller\Route::Put
     * @covers \App\Controller\Route::Delete
     * @covers \App\Controller\Route::Group
     */
    public function testGroupRoute() : void
    {
        $_ENV['DEBUG'] = true;
        $_SERVER['REQUEST_URI'] = '/test/test2';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Route::Group('/test', function () {
            self::assertTrue(Route::Get('/test2', App\Controller\HomeController::class."@index"));
        });
    }
}