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
}

function view(string $viewname, array $viewargs) {
    return "$viewname:".implode(',', $viewargs);
}