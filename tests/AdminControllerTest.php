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
        $this->csrfProtection->method("tokenInput")->willReturn(
            "<input type=\"hidden\" name=\"xh_csrf_token\" value=\"9b923b4ec202f67066fe734993b6a9a4\">"
        );
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["boilerplate"]);
        $this->sut = $this->getMockBuilder(AdminController::class)
            ->setConstructorArgs(["600", $this->textBlocks, $this->csrfProtection, $this->view])
            ->onlyMethods(["initEditor"])
            ->getMock();
    }

    public function testNewTextBlockInvalidName(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(false);
        $response = ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "not valid"]]), "new");
        $this->assertStringContainsString("The text block name “not valid” is invalid.", $response->output());
    }

    public function testNewTextBlockExists(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(true);
        $response = ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "foo"]]), "new");
        $this->assertStringContainsString("The text block “foo“ exists already.", $response->output());
    }

    public function testNewTextBlockWriteFailure(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(false);
        $this->textBlocks->method("write")->willReturn(false);
        $response = ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "foo"]]), "new");
        $this->assertStringContainsString("The text block “foo” could not be saved.", $response->output());
    }

    public function testNewTextBlockSuccess(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(false);
        $this->textBlocks->method("write")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&boilerplate&admin=plugin_main&action=edit&boilerplate_name=foo",
            "post" => ["boilerplate_name" => "foo"],
        ]);
        $response = ($this->sut)($request, "new");
        $this->assertSame(
            "http://example.com/?&boilerplate&admin=plugin_main&action=edit&boilerplate_name=foo",
            $response->location()
        );
    }

    public function testEditTextBlockReadFailure(): void
    {
        $this->textBlocks->method("read")->willReturn(false);
        $response = ($this->sut)(new FakeRequest(["url" => "http://example.com/?&boilerplate_name=foo"]), "edit");
        $this->assertStringContainsString("The text block “foo” could not be read.", $response->output());
    }

    public function testEditTextBlockSuccess(): void
    {
        $this->textBlocks->method("read")->willReturn("<p>some content</p>");
        $this->sut->expects($this->once())->method("initEditor");
        $request = new FakeRequest([
            "url" => "http://example.com/?&boilerplate&admin=plugin_main&boilerplate_name=foo",
        ]);
        Approvals::verifyHtml(($this->sut)($request, "edit")->output());
    }

    public function testSaveTextBlockWriteFailure(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("write")->willReturn(false);
        $request = new FakeRequest(["post" => ["boilerplate_name" => "foo", "boilerplate_text" => "<p>some text</p>"]]);
        $response = ($this->sut)($request, "save");
        $this->assertStringContainsString("The text block “foo” could not be saved.", $response->output());
    }

    public function testSaveTextBlockSuccess(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("write")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&boilerplate&admin=plugin_main&boilerplate_name=foo",
        ]);
        $response = ($this->sut)($request, "save");
        $this->assertSame(
            "http://example.com/?&boilerplate&admin=plugin_main&boilerplate_name=foo&action=plugin_tx",
            $response->location()
        );
    }

    public function testDeleteTextBlockFailure(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("delete")->willReturn(false);
        $response = ($this->sut)(new FakeRequest(["post" => ["boilerplate_name" => "foo"]]), "delete");
        $this->assertStringContainsString("The text block “foo” could not be deleted.", $response->output());
    }

    public function testDeleteTextBlockSuccess(): void
    {
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("delete")->with("foo")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&boilerplate&admin=plugin_main",
            "post" => ["boilerplate_name" => "foo"],
        ]);
        $response = ($this->sut)($request, "delete");
        $this->assertSame("http://example.com/?&boilerplate&admin=plugin_main&action=plugin_tx", $response->location());
    }

    public function testRenderMainAdministration(): void
    {
        $this->textBlocks->method("names")->willReturn(["foo", "bar"]);
        $request = new FakeRequest(["url" => "http://example.com/?&boilerplate&admin=plugin_main&action=plugin_tx"]);
        Approvals::verifyHtml(($this->sut)($request, "")->output());
    }
}
