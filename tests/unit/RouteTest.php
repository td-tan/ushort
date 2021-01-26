<?php declare(strict_types=1);

use App\Helper\Route;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
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
}

