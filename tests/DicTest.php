<?php

namespace Boilerplate;

use PHPUnit\Framework\TestCase;
use XH\CSRFProtection;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $sn, $pth, $cf, $plugin_tx, $_XH_csrfProtection;

        $sn = "/";
        $pth = ["folder" => ["base" => "./", "plugins" => "./plugins/"]];
        $cf = ["editor" => ["height" => "600"]];
        $plugin_tx = ["boilerplate" => []];
        $_XH_csrfProtection = $this->createStub(CSRFProtection::class);
    }

    public function testMakeBoilerplateController(): void
    {
        $this->assertInstanceOf(BoilerplateController::class, Dic::makeBoilerplateController());
    }

    public function testMakeInfoController(): void
    {
        $this->assertInstanceOf(InfoController::class, Dic::makeInfoController());
    }

    public function testMakeAdminController(): void
    {
        $this->assertInstanceOf(AdminController::class, Dic::makeAdminController());
    }
}
