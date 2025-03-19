<?php

/**
 * Copyright (c) Christoph M. Becker
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

use Boilerplate\Model\TextBlocks;
use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public static function makeBoilerplateController(): BoilerplateController
    {
        return new BoilerplateController(self::textBlocks(), self::view());
    }

    public static function makeInfoController(): InfoController
    {
        global $pth;

        return new InfoController(
            $pth['folder']['plugins'] . 'boilerplate/',
            self::textBlocks(),
            new SystemChecker(),
            self::view()
        );
    }

    public static function makeAdminController(): AdminController
    {
        global $cf, $_XH_csrfProtection;

        return new AdminController(
            $cf["editor"]["height"],
            self::textBlocks(),
            $_XH_csrfProtection,
            self::view()
        );
    }

    private static function textBlocks(): TextBlocks
    {
        global $pth;

        return new TextBlocks($pth["folder"]["base"] . "content/boilerplate/");
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;

        return new View($pth["folder"]["plugins"] . "boilerplate/views/", $plugin_tx["boilerplate"]);
    }
}
