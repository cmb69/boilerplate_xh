<?php

/**
 * Front-end of Boilerplate_XH.
 *
 * @package	Boilerplate
 * @copyright	Copyright (c) 2012-2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Boilerplate_XH
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
define('BOILERPLATE_VERSION', '1beta1');


/**
 * The base URI.
 */
define('BOILERPLATE_URL', 'http'
   . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
   . '://' . $_SERVER['SERVER_NAME']
   . ($_SERVER['SERVER_PORT'] < 1024 ? '' : ':' . $_SERVER['SERVER_PORT'])
   . preg_replace('/index.php$/', '', $_SERVER['SCRIPT_NAME']));


/*
 * For backward compatibility CMSimple_XH < 1.5
 */
if (!function_exists('evaluate_scripting')) {
    function evaluate_scripting($txt)
    {
	return $txt;
    }
}


/**
 * Returns a text with special characters converted to HTML entities.
 *
 * @param  string $text
 * @return string  The (X)HTML.
 */
function Boilerplate_hsc($text)
{
    return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
}


/**
 * Returns the data folder path. Creates it, if necessary.
 * Emits error messages on failure.
 *
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the plugins.
 * @return string
 */
function Boilerplate_dataFolder()
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['boilerplate'];
    if ($pcf['folder_data'] == '') {
	$fn = $pth['folder']['plugins'] . 'boilerplate/data/';
    } else {
	$fn = $pth['folder']['base'] . rtrim('/', $pcf['folder_data']) . '/';
    }
    if (file_exists($fn)) {
	if (!is_dir($fn)) {
	    e('cntopen', 'folder', $fn);
	}
    } else {
	if (!mkdir($fn, 0777, true)) {
	    e('cntwriteto', 'folder', $fn);
	}
    }
    return $fn;
}


/**
 * Returns the path of a boilerplate file.
 *
 * @param  string $name  The name of the text block.
 * @return string
 */
function Boilerplate_filename($name)
{
    return Boilerplate_dataFolder() . $name . '.dat';
}

/**
 * Returns whether the given $name is valid.
 * Emits an error message, if the name is invalid.
 *
 * @global string  Error messages to add to the (X)HTML.
 * @global array  The localization of the plugins.
 * @param  string $name
 * @return bool
 */
function Boilerplate_validName($name)
{
    global $e, $plugin_tx;

    $ptx = $plugin_tx['boilerplate'];
    $valid = preg_match('/^[a-z0-9_\-]+$/su', $name);
    if (!$valid) {
	$e .= '<li><b>' . $ptx['error_invalid_name'] . '</b>' . tag('br')
	    . $name . '</li>' . PHP_EOL;
    }
    return $valid;
}


/**
 * Returns a text block.
 * On failure a error message is emitted and false is returned.
 *
 * @access public
 *
 * @param  string $name
 * @return string  The (X)HTML.
 */
function Boilerplate($name)
{
    if (!Boilerplate_validName($name)) {
	return false;
    }
    $fn = Boilerplate_filename($name);
    if (is_readable($fn)
	&& ($content = file_get_contents($fn)) !== false)
    {
	return evaluate_scripting($content);
    } else {
	e('cntopen', 'file', $fn);
	return false;
    }
}

?>
