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
 * Creates a new boilerplate file.
 * Redirects to the edit view on success;
 * shows the main administration on failure.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function Boilerplate_new($name)
{
    if (!Boilerplate_validName($name)) {
	return Boilerplate_admin();
    }
    $fn = Boilerplate_filename($name);
    if (!file_exists($fn)) {
	if (($fh = fopen($fn, 'x')) !== false) {
	    fclose($fh);
	    $qs = '?boilerplate&admin=plugin_main&action=edit&boilerplate_name='
		. $name;
	    header('Location: ' . BOILERPLATE_URL . $qs, true, 303);
	    exit;
	} else {
	    e('cntwriteto', 'file', $fn);
	    return Boilerplate_admin();
	}
    } else {
	e('alreadyexists', 'file', $fn);
	return Boilerplate_admin();
    }
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
    $labels = array(
	'heading' => "Boilerplate: $name",
	'save' => ucfirst($tx['action']['save'])
    );
    $url = "$sn?&amp;boilerplate";
    $editorHeight = $cf['editor']['height'];
    $content = Boilerplate_hsc($content);
    $showSubmit = !function_exists('init_editor')
	|| $cf['editor']['external'] == '';
    $bag = compact('labels', 'name', 'url', 'editorHeight', 'content',
		   'showSubmit');
    $o = Boilerplate_render('edit', $bag);
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
 * Deletes the boilerplate $name.
 * Redirects to the main administration view on success;
 * shows the main administration on failure.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function Boilerplate_delete($name)
{
    $fn = Boilerplate_filename($name);
    if (unlink($fn)) {
	$qs = '?boilerplate&admin=plugin_main&action=plugin_tx';
	header('Location: ' . BOILERPLATE_URL . $qs, true, 303);
	exit;
    } else {
	e('cntdelete', 'file', $fn);
	return Boilerplate_admin();
    }
}


/**
 * Renders a template.
 *
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the core.
 * @param  string  $_template  The name of the template.
 * @param  string  $_bag  Variables available in the template.
 * @return string  The (X)HTML.
 */
function Boilerplate_render($_template, $_bag)
{
    global $pth, $cf;

    $_template = "{$pth['folder']['plugins']}boilerplate/views/$_template.htm";
    $_xhtml = $cf['xhtml']['endtags'];
    unset($pth, $cf);
    extract($_bag);
    ob_start();
    include $_template;
    $o = ob_get_clean();
    if (!$_xhtml) {
	$o = str_replace('/>', '>', $o);
    }
    return $o;
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
	$name = basename($file, '.htm');
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
	    $o .= Boilerplate_save(stsl($_POST['boilerplate_name']));
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
