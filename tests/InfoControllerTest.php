<?php

namespace Boilerplate;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoControllerTest extends TestCase
{
    /** @var TextBlocks&Stub */
    private $textBlocks;

    /** @var View */
    private $view;

    /** @var InfoController */
    private $sut;

    public function setUp(): void
    {
        $this->textBlocks = $this->createStub(TextBlocks::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["boilerplate"]);
        $this->sut = new InfoController(
            "./plugins/boilerplate/",
            $this->textBlocks,
            new FakeSystemChecker(),
            $this->view
        );
    }

    public function testRenderInfo(): void
    {
        $this->textBlocks->method("getDataFolder")->willReturn("./content/boilerplate/");
        Approvals::verifyHtml(($this->sut)()->output());
    }
}
