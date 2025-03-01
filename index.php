<?php

/**
 * Copyright 2012-2021 Christoph M. Becker
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

use Boilerplate\BoilerplateController;
use Boilerplate\TextBlocks;

const BOILERPLATE_VERSION = '2.1-dev';

function boilerplate(string $name): string
{
    global $pth, $plugin_tx;

    $textBlocks = new TextBlocks("{$pth['folder']['base']}content/boilerplate/");
    $controller = new BoilerplateController($plugin_tx["boilerplate"], $textBlocks);
    return $controller($name);
}
