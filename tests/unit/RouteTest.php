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
}

