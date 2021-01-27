<?php declare(strict_types=1);

use App\Controller\HomeController;
use App\Helper\RequestData;
use PHPUnit\Framework\TestCase;

final class HomeControllerTest extends TestCase
{
    /**
     * @covers App\Controller\HomeController
     */
    public function testIndex() : void
    {
        $homeCtrl = new HomeController();
        self::assertEquals("pages.home:", $homeCtrl->index(new RequestData()));

        self::assertEquals("pages.home:", $homeCtrl->index(new RequestData(['test' => '123'])));
    }

    /**
     * @covers App\Controller\HomeController
     */
    public function testShort() : void
    {
        $homeCtrl = new HomeController();
        self::assertEquals("", $homeCtrl->short(new RequestData()));

        self::assertEquals("123", $homeCtrl->short(new RequestData(['id' => '123'])));
    }
}

function view(string $viewname, array $viewargs) {
    return "$viewname:".implode(',', $viewargs);
}