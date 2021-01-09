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
 * The data folder.
 */
define(
    'BOILERPLATE_DATA_FOLDER',
    empty($plugin_cf['boilerplate']['folder_data'])
    ? $pth['folder']['plugins'] . 'boilerplate/data/'
    : $pth['folder']['base'] . $plugin_cf['boilerplate']['folder_data']
);

/**
 * Returns a text block. On failure a error message is emitted and false is
 * returned.
 *
 * @param string $name A boilerplate name.
 *
 * @return string (X)HTML.
 *
 * @global string            Error messages to emit in the (X)HTML.
 * @global array             The localization of the plugins.
 * @global Boilerplate\Model The model.
 */
function boilerplate($name)
{
    global $e, $plugin_tx, $_Boilerplate;

    $ptx = $plugin_tx['boilerplate'];
    if (!$_Boilerplate->isValidName($name)) {
        $e .= '<li><b>' . $ptx['error_invalid_name'] . '</b><br>'
            . $name . '</li>' . PHP_EOL;
        return false;
    }
    $content = $_Boilerplate->read($name);
    if ($content !== false) {
        if (function_exists('evaluate_scripting')) {
            $content = evaluate_scripting($content);
        }
        return $content;
    } else {
        e('cntopen', 'file', $_Boilerplate->filename($name));
        return false;
    }
}

$_Boilerplate = new Boilerplate\Model(BOILERPLATE_DATA_FOLDER);
