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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
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
        $this->sut = new InfoController("./plugins/boilerplate/", $this->textBlocks, $this->view);
    }

    public function testRenderInfo(): void
    {
        $this->textBlocks->method("getDataFolder")->willReturn("./content/boilerplate/");
        Approvals::verifyHtml($this->sut->renderInfo());
    }
}
