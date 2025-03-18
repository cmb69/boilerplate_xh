<?php

namespace Boilerplate;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\View;

class BoilerplateControllerTest extends TestCase
{
    /** @var TextBlocks&Stub */
    private $textBlocks;

    /** @var View */
    private $view;

    /** @var BoilerplateController&MockObject */
    private $sut;

    public function setUp(): void
    {
        $this->textBlocks = $this->createStub(TextBlocks::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["boilerplate"]);
        $this->sut = $this->getMockBuilder(BoilerplateController::class)
        ->setConstructorArgs([$this->textBlocks, $this->view])
        ->onlyMethods(["evaluateScripting"])
        ->getMock();
    }

    public function testInvalidName(): void
    {
        $this->textBlocks->method("isValidName")->willReturn(false);
        $this->assertStringContainsString("The text block name “not allowed” is invalid.", ($this->sut)("not allowed"));
    }

    public function testReadFailure(): void
    {
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("read")->willReturn(false);
        $this->assertStringContainsString("The text block “bar” could not be read.", ($this->sut)("bar"));
    }

    public function testSuccess(): void
    {
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("read")->willReturn("<p>this &amp; that</p>");
        $this->sut->method("evaluateScripting")->willReturn("<p>this &amp; that</p>");
        $this->assertSame("<p>this &amp; that</p>", ($this->sut)("foo"));
    }
}
