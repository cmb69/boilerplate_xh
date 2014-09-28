<?php

/**
 * Front-end of Boilerplate_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Boilerplate
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Boilerplate_XH
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
 * The base URI.
 */
define(
    'BOILERPLATE_URL',
    'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://' . $_SERVER['SERVER_NAME']
    . ($_SERVER['SERVER_PORT'] < 1024 ? '' : ':' . $_SERVER['SERVER_PORT'])
    . preg_replace('/index.php$/', '', $_SERVER['SCRIPT_NAME'])
);

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
 * The model class.
 */
require_once $pth['folder']['plugin_classes'] . 'model.php';

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
 * @global Boilerplate_Model The model.
 */
function boilerplate($name)
{
    global $e, $plugin_tx, $_Boilerplate;

    $ptx = $plugin_tx['boilerplate'];
    if (!$_Boilerplate->isValidName($name)) {
        $e .= '<li><b>' . $ptx['error_invalid_name'] . '</b>' . tag('br')
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

$_Boilerplate = new Boilerplate_Model(BOILERPLATE_DATA_FOLDER);

?>
