<?php

namespace Boilerplate;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use XH\CSRFProtection;

class AdminControllerTest extends TestCase
{
    /** @var TextBlocks&Stub */
    private $textBlocks;

    /** @var CSRFProtection */
    private $csrfProtection;

    /** @var View */
    private $view;

    /** @var AdminController&MockObject */
    private $sut;

    public function setUp(): void
    {
        $this->textBlocks = $this->createStub(TextBlocks::class);
        $this->csrfProtection = $this->createStub(CSRFProtection::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["boilerplate"]);
        $this->sut = $this->getMockBuilder(AdminController::class)
            ->setConstructorArgs(["/", "600", $this->textBlocks, $this->csrfProtection, $this->view])
            ->onlyMethods(["initEditor", "relocate"])
            ->getMock();
    }

    public function testNewTextBlockInvalidName(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(false);
        $this->assertStringContainsString(
            "The text block name “not valid” is invalid.",
            ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "not valid"]]), "new")
        );
    }

    public function testNewTextBlockExists(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(true);
        $this->assertStringContainsString(
            "The text block “foo“ exists already.",
            ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "foo"]]), "new")
        );
    }

    public function testNewTextBlockWriteFailure(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(false);
        $this->textBlocks->method("write")->willReturn(false);
        $this->assertStringContainsString(
            "The text block “foo” could not be saved.",
            ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "foo"]]), "new")
        );
    }

    public function testNewTextBlockSuccess(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(false);
        $this->textBlocks->method("write")->willReturn(true);
        $this->sut->expects($this->once())->method("relocate")->with(
            "?boilerplate&admin=plugin_main&action=edit&boilerplate_name=foo"
        );
        ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "foo"]]), "new");
    }

    public function testEditTextBlockReadFailure(): void
    {
        $this->textBlocks->method("read")->willReturn(false);
        $this->assertStringContainsString(
            "The text block “foo” could not be read.",
            ($this->sut)(new FakeRequest(["url" => "http://example.com/?&boilerplate_name=foo"]), "edit")
        );
    }

    public function testEditTextBlockSuccess(): void
    {
        $this->textBlocks->method("read")->willReturn("<p>some content</p>");
        $this->csrfProtection->method("tokenInput")->willReturn(
            "<input type=\"hidden\" name=\"xh_csrf_token\" value=\"9b923b4ec202f67066fe734993b6a9a4\">"
        );
        $this->sut->expects($this->once())->method("initEditor");
        Approvals::verifyHtml(($this->sut)(new FakeRequest(["url" => "http://example.com/?&boilerplate_name=foo"]), "edit"));
    }

    public function testSaveTextBlockWriteFailure(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("write")->willReturn(false);
        $request = new FakeRequest(["post" => ["boilerplate_name" => "foo", "boilerplate_text" => "<p>some text</p>"]]);
        $this->assertStringContainsString(
            "The text block “foo” could not be saved.",
            ($this->sut)($request, "save")
        );
    }

    public function testSaveTextBlockSuccess(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("write")->willReturn(true);
        $this->sut->expects($this->once())->method("relocate")->with(
            "?boilerplate&admin=plugin_main&action=plugin_tx"
        );
        ($this->sut)(new FakeRequest(), "save");
    }

    public function testDeleteTextBlockFailure(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("delete")->willReturn(false);
        $this->assertStringContainsString(
            "The text block “foo” could not be deleted.",
            ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "foo"]]), "delete")
        );
    }

    public function testDeleteTextBlockSuccess(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("delete")->with("foo")->willReturn(true);
        $this->sut->expects($this->once())->method("relocate")->with(
            "?boilerplate&admin=plugin_main&action=plugin_tx"
        );
        ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "foo"]]), "delete");
    }

    public function testRenderMainAdministration(): void
    {
        $this->textBlocks->method("names")->willReturn(["foo", "bar"]);
        Approvals::verifyHtml(($this->sut)(new FakeRequest(), ""));
    }
}
