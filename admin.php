<?php

/**
 * Back-end of Boilerplate_XH.
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
 * Returns the version information view.
 *
 * @global array  The paths of system files and folders.
 * @return string  The (X)HTML.
 */
function Boilerplate_version()
{
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Boilerplate_XH">Boilerplate_XH</a></h1>'
	. tag('img src="' . $pth['folder']['plugins'] . 'boilerplate/boilerplate.png" alt="Plugin icon" style="float: left; margin: 0 16px 0 0"')
	. '<p style="margin-top: 1em">Version: ' . BOILERPLATE_VERSION . '</p>'
	. '<p>Copyright &copy; 2012-2013 <a href="http://3-magi.net/">Christoph M. Becker</a></p>'
	. '<p style="text-align: justify">This program is free software: you can redistribute it and/or modify'
	. ' it under the terms of the GNU General Public License as published by'
	. ' the Free Software Foundation, either version 3 of the License, or'
	. ' (at your option) any later version.</p>'
	. '<p style="text-align: justify">This program is distributed in the hope that it will be useful,'
	. ' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	. ' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	. ' GNU General Public License for more details.</p>'
	. '<p style="text-align: justify">You should have received a copy of the GNU General Public License'
	. ' along with this program.  If not, see'
	. ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>';
}


/**
 * Returns the requirements information view.
 *
 * @global array  The paths of system files and folders.
 * @global array  The localization of the core.
 * @global array  The localization of the plugins.
 * @return string  The (X)HTML.
 */
function boilerplate_systemCheck() // RELEASE-TODO
{
    global $pth, $tx, $plugin_tx;

    $phpVersion = '4.3.0';
    $ptx = $plugin_tx['boilerplate'];
    $imgdir = $pth['folder']['plugins'] . 'boilerplate/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = '<h4>' . $ptx['syscheck_title'] . '</h4>'
	. (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
	. '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
	. tag('br');
    foreach (array('pcre') as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
	    . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext) . tag('br');
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
	. '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br') . tag('br');
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	. '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br') . tag('br');
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'] . 'boilerplate/' . $folder;
    }
    $folders[] = Boilerplate_dataFolder();
    foreach ($folders as $folder) {
	$o .= (is_writable($folder) ? $ok : $warn)
	    . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder) . tag('br');
    }
    return $o;
}


/**
 * Creates a new boilerplate file, if it doesn't exist. Returns the edit view.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function Boilerplate_new($name)
{
    if (!Boilerplate_validName($name)) {
	return Boilerplate_admin($name);
    }
    $fn = Boilerplate_filename($name);
    if (!file_exists($fn)) {
	if (($fh = fopen($fn, 'x')) === false) {
	    e('cntwriteto', 'file', $fn);
	} else {
	    fclose($fh);
	}
    }
    return Boilerplate_edit($name);
}


/**
 * Returns the boilerplate edit view.
 *
 * @global string  The site name.
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the core.
 * @global array  The localization of the core.
 * @param string $name
 * @return string  The (X)HTML.
 */
function Boilerplate_edit($name)
{
    global $sn, $pth, $cf, $tx;

    $fn = Boilerplate_filename($name);
    if (($content = file_get_contents($fn)) === false) {
	e('cntopen', 'file', $fn);
	return false;
    }
    $action = $sn . '?&amp;boilerplate&amp;admin=plugin_main&amp;action=save&amp;boilerplate_name=' . urlencode($name);
    $o = '<div class="plugineditcaption">Boilerplate: ' . $name . '</div>'
	. '<form class="plugineditform" action="' . $action . '" method="post">'
	. '<textarea class="plugintextarea" name="boilerplate_text" style="height: ' . $cf['editor']['height'] . 'px">'
	. htmlspecialchars($content, ENT_NOQUOTES, 'UTF-8') . '</textarea>'
	. (!function_exists('init_editor') || $cf['editor']['external'] == ''
	    ? tag('input type="submit" class="submit" value="' . ucfirst($tx['action']['save']) . '"')
	    : '')
	. '</form>';
    init_editor(array('plugintextarea'));
    return $o;
}


/**
 * Saves the boilerplate. Returns the main administration view.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function Boilerplate_save($name)
{
    $fn = Boilerplate_filename($name);
    if (($fh = fopen($fn, 'w')) === false
	|| fwrite($fh, stsl($_POST['boilerplate_text'])) === false)
    {
	e('cntsave', 'file', $fn);
    }
    if ($fh) {
	fclose($fh);
    }
    return Boilerplate_admin();
}


/**
 * Deletes the boilerplate $name. Returns the main administration view.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function boilerplate_delete($name)
{
    $fn = Boilerplate_filename($name);
    if (!unlink($fn)) {
	e('cntdelete', 'file', $fn);
    }
    return Boilerplate_admin();
}


/**
 * Renders a template.
 *
 * @global array  The paths of system files and folders.
 * @param  string  $_template  The name of the template.
 * @param  string  $_bag  Variables available in the template.
 * @return string  The (X)HTML.
 */
function Boilerplate_render($_template, $_bag)
{
    global $pth;

    $_template = "{$pth['folder']['plugins']}boilerplate/views/$_template.htm";
    unset($pth);
    extract($_bag);
    ob_start();
    include $_template;
    return ob_get_clean(); // TODO: xhtml:endtags
}


/**
 * Returns the main administration view.
 *
 * @global array  The paths of system files and folders.
 * @global string  The script name.
 * @global array  The localization of the core.
 * @global array  The localization of the plugins.
 * @return string  The (X)HTML.
 */
function Boilerplate_admin()
{
    global $pth, $sn, $tx, $plugin_tx;

    $ptx = $plugin_tx['boilerplate'];
    $labels = array(
	'heading' => "Boilerplate: $ptx[menu_main]",
	'edit' => ucfirst($tx['action']['edit']),
	'delete' => ucfirst($tx['action']['delete']),
	'create' => $ptx['label_create'],
	'confirm' => addcslashes($ptx['confirm_delete'], "\r\n\\\'")
    );
    $labels = array_map('Boilerplate_hsc', $labels);
    $deleteImage = $pth['folder']['plugins'] . 'boilerplate/images/delete.png';
    $url = $sn.'?&amp;boilerplate';
    $baseURL = $sn.'?&amp;boilerplate&amp;admin=plugin_main&amp;action=';
    $boilerplates = array();
    foreach (glob(Boilerplate_filename('*')) as $file) {
	$name = basename($file, '.dat');
	$boilerplates[$name] = array(
	    'editURL' => $baseURL . 'edit&amp;boilerplate_name=' . $name,
	    'deleteURL' => $baseURL . 'delete&amp;boilerplate_name=' . $name
	);
    }
    $bag = compact('labels', 'deleteImage', 'url', 'boilerplates');
    return Boilerplate_render('admin', $bag);
}


/*
 * Handle the plugin administration.
 */
if (isset($boilerplate) && $boilerplate == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
	$o .= Boilerplate_version() . tag('hr') . Boilerplate_systemCheck();
	break;
    case 'plugin_main':
	switch ($action) {
	case 'new':
	    $o .= Boilerplate_new(stsl($_POST['boilerplate_name']));
	    break;
	case 'edit':
	    $o .= Boilerplate_edit(stsl($_GET['boilerplate_name']));
	    break;
	case 'save':
	    $o .= Boilerplate_save(stsl($_GET['boilerplate_name']));
	    break;
	case 'delete':
	    $o .= Boilerplate_delete(stsl($_POST['boilerplate_name']));
	    break;
	default: $o .= Boilerplate_admin();
	}
	break;
    default:
	$o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
