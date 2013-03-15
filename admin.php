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
 * Saves a boilerplate text.
 * Redirects to the main administration on success;
 * returns the edit view on failure.
 *
 * @param  string $name
 * @return string  The (X)HTML.
 */
function Boilerplate_save($name)
{
    $fn = Boilerplate_filename($name);
    $content = stsl($_POST['boilerplate_text']);
    $ok = ($fh = fopen($fn, 'w')) !== false
	&& fwrite($fh, $content) !== false;
    if ($fh) {
	fclose($fh);
    }
    if ($ok) {
	$qs = '?boilerplate&admin=plugin_main&action=plugin_tx';
	header('Location: ' . BOILERPLATE_URL . $qs, true, 303);
	exit;
    } else {
	e('cntsave', 'file', $fn);
        return Boilerplate_edit($name, $content);
    }
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
 * Returns the plugin information view.
 *
 * @return string  The (X)HTML.
 */
function Boilerplate_info() // RELEASE-TODO
{
    global $pth, $tx, $plugin_tx;

    $ptx = $plugin_tx['boilerplate'];
    $labels = array(
	'syscheck' => $ptx['syscheck_title'],
	'about' => $ptx['about']
    );
    $labels = array_map('Boilerplate_hsc', $labels);
    $phpVersion = '4.3.0';
    foreach (array('ok', 'warn', 'fail') as $state) {
        $images[$state] = $pth['folder']['plugins']
	    . "boilerplate/images/$state.png";
    }
    $checks = array();
    $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)] =
        version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
    foreach (array('pcre') as $ext) {
	$checks[sprintf($ptx['syscheck_extension'], $ext)] =
	    extension_loaded($ext) ? 'ok' : 'fail';
    }
    $checks[$ptx['syscheck_magic_quotes']] =
        !get_magic_quotes_runtime() ? 'ok' : 'fail';
    $checks[$ptx['syscheck_encoding']] =
        strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
    foreach (array('config/', 'css', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'] . 'boilerplate/' . $folder;
    }
    $folders[] = Boilerplate_dataFolder();
    foreach ($folders as $folder) {
	$checks[sprintf($ptx['syscheck_writable'], $folder)] =
            is_writable($folder) ? 'ok' : 'warn';
    }
    $icon = $pth['folder']['plugins'] . 'boilerplate/boilerplate.png';
    $version = BOILERPLATE_VERSION;
    $bag = compact('labels', 'images', 'checks', 'icon', 'version');
    return Boilerplate_render('info', $bag);
}


/**
 * Returns the boilerplate edit view.
 *
 * @global string  The site name.
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the core.
 * @global array  The localization of the core.
 * @param string $name
 * @param string $content
 * @return string  The (X)HTML.
 */
function Boilerplate_edit($name, $content = null)
{
    global $sn, $pth, $cf, $tx;

    $fn = Boilerplate_filename($name);
    if (!isset($content)) {
	if (($content = file_get_contents($fn)) === false) {
	    e('cntopen', 'file', $fn);
	    return false;
	}
    }
    $labels = array(
	'heading' => "Boilerplate: $name",
	'save' => ucfirst($tx['action']['save'])
    );
    $labels = array_map('Boilerplate_hsc', $labels);
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
	$o .= Boilerplate_info();
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
