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

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The version number of the plugin.
 */
define('BOILERPLATE_VERSION', '@BOILERPLATE_VERSION@');

/**
 * Returns a text block. On failure a error message is emitted and false is
 * returned.
 *
 * @param string $name A boilerplate name.
 *
 * @return string (X)HTML.
 */
function boilerplate($name)
{
    global $pth, $e, $plugin_tx;

    $model = new Boilerplate\Model("{$pth['folder']['base']}content/boilerplate/");
    $ptx = $plugin_tx['boilerplate'];
    if (!$model->isValidName($name)) {
        $e .= '<li><b>' . $ptx['error_invalid_name'] . '</b><br>'
            . $name . '</li>' . PHP_EOL;
        return false;
    }
    $content = $model->read($name);
    if ($content !== false) {
        return  evaluate_scripting($content);
    } else {
        e('cntopen', 'file', $model->filename($name));
        return false;
    }
}
