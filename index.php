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

const BOILERPLATE_VERSION = '2.0';

function boilerplate(string $name): string
{
    global $pth, $plugin_tx;

    $model = new Boilerplate\TextBlocks("{$pth['folder']['base']}content/boilerplate/");
    $ptx = $plugin_tx['boilerplate'];
    if (!$model->isValidName($name)) {
        return XH_message('fail', $ptx['error_invalid_name'], $name);
    }
    $content = $model->read($name);
    if ($content !== false) {
        return evaluate_scripting($content);
    } else {
        return XH_message('fail', $ptx['error_cant_read'], $name);
    }
}
