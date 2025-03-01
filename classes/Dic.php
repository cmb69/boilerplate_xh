<?php

/**
 * Copyright 2013-2025 Christoph M. Becker
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

class Dic
{
    public static function makeBoilerplateController(): BoilerplateController
    {
        global $pth, $plugin_tx;

        return new BoilerplateController(
            new TextBlocks("{$pth['folder']['base']}content/boilerplate/"),
            new View("{$pth['folder']['base']}content/boilerplate/views/", $plugin_tx["boilerplate"])
        );
    }

    public static function makeAdminController(): AdminController
    {
        global $pth, $plugin_tx, $_XH_csrfProtection;

        return new AdminController(
            new TextBlocks("{$pth['folder']['base']}content/boilerplate/"),
            $_XH_csrfProtection,
            new View(
                "{$pth['folder']['plugins']}boilerplate/views/",
                $plugin_tx["boilerplate"]
            )
        );
    }
}
