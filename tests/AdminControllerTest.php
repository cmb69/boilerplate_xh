<?php

/**
 * Copyright 2025 Christoph M. Becker
 *
 * This file is part of Boilerplate_XH.
 *
 * Boilerplate_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Boilerplate_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Boilerplate_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Boilerplate;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
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
        $_POST = ["boilerplate_name" => "not valid"];
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(false);
        $this->assertStringContainsString(
            "The text block name “not valid” is invalid.",
            ($this->sut)("new")
        );
    }

    public function testNewTextBlockExists(): void
    {
        $_POST = ["boilerplate_name" => "foo"];
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(true);
        $this->assertStringContainsString(
            "The text block “foo“ exists already.",
            ($this->sut)("new")
        );
    }

    public function testNewTextBlockWriteFailure(): void
    {
        $_POST = ["boilerplate_name" => "foo"];
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(false);
        $this->textBlocks->method("write")->willReturn(false);
        $this->assertStringContainsString(
            "The text block “foo” could not be saved.",
            ($this->sut)("new")
        );
    }

    public function testNewTextBlockSuccess(): void
    {
        $_POST = ["boilerplate_name" => "foo"];
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("isValidName")->willReturn(true);
        $this->textBlocks->method("exists")->willReturn(false);
        $this->textBlocks->method("write")->willReturn(true);
        $this->sut->expects($this->once())->method("relocate")->with(
            "?boilerplate&admin=plugin_main&action=edit&boilerplate_name=foo"
        );
        ($this->sut)("new");
    }

    public function testEditTextBlockReadFailure(): void
    {
        $_GET = ["boilerplate_name" => "foo"];
        $this->textBlocks->method("read")->willReturn(false);
        $this->assertStringContainsString("The text block “foo” could not be read.", ($this->sut)("edit"));
    }

    public function testEditTextBlockSuccess(): void
    {
        $_GET = ["boilerplate_name" => "foo"];
        $this->textBlocks->method("read")->willReturn("<p>some content</p>");
        $this->csrfProtection->method("tokenInput")->willReturn(
            "<input type=\"hidden\" name=\"xh_csrf_token\" value=\"9b923b4ec202f67066fe734993b6a9a4\">"
        );
        $this->sut->expects($this->once())->method("initEditor");
        Approvals::verifyHtml(($this->sut)("edit"));
    }

    public function testSaveTextBlockWriteFailure(): void
    {
        $_POST = ["boilerplate_name" => "foo", "boilerplate_text" => "<p>some text</p>"];
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("write")->willReturn(false);
        $this->assertStringContainsString(
            "The text block “foo” could not be saved.",
            ($this->sut)("save")
        );
    }

    public function testSaveTextBlockSuccess(): void
    {
        $_POST = ["boilerplate_name" => "foo", "boilerplate_text" => "<p>some text</p>"];
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("write")->willReturn(true);
        $this->sut->expects($this->once())->method("relocate")->with(
            "?boilerplate&admin=plugin_main&action=plugin_tx"
        );
        ($this->sut)("save");
    }

    public function testDeleteTextBlockFailure(): void
    {
        $_POST = ["boilerplate_name" => "foo"];
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("delete")->willReturn(false);
        $this->assertStringContainsString(
            "The text block “foo” could not be deleted.",
            ($this->sut)("delete")
        );
    }

    public function testDeleteTextBlockSuccess(): void
    {
        $_POST = ["boilerplate_name" => "foo"];
        $this->csrfProtection->expects($this->once())->method("check");
        $this->textBlocks->method("delete")->willReturn(true);
        $this->sut->expects($this->once())->method("relocate")->with(
            "?boilerplate&admin=plugin_main&action=plugin_tx"
        );
        ($this->sut)("delete");
    }

    public function testRenderMainAdministration(): void
    {
        $this->textBlocks->method("names")->willReturn(["foo", "bar"]);
        Approvals::verifyHtml(($this->sut)(""));
    }
}
