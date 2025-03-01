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
