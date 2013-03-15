<?php

/**
 * Back-end of Boilerplate_XH.
 *
 * Copyright (c) 2012-2013 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns the version information view.
 *
 * @return string  The (X)HTML.
 */
function boilerplate_version() {
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Boilerplate_XH">Boilerplate_XH</a></h1>'."\n"
	    .tag('img class="boilerplate_plugin_icon" src="'.$pth['folder']['plugins'].'boilerplate/boilerplate.png" alt="Plugin icon"')."\n"
	    .'<p style="margin-top: 1em">Version: '.BOILERPLATE_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2012-2013 <a href="http://3-magi.net/">Christoph M. Becker</a></p>'."\n"
	    .'<p class="boilerplate_license">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p class="boilerplate_license">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p class="boilerplate_license">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns the requirements information view.
 *
 * @return string  The (X)HTML.
 */
function boilerplate_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('BOILERPLATE_PHP_VERSION', '4.3.0');
    $ptx = $plugin_tx['boilerplate'];
    $imgdir = $pth['folder']['plugins'].'boilerplate/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $o = '<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, BOILERPLATE_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], BOILERPLATE_PHP_VERSION)
	    .tag('br')."\n";
    foreach (array('pcre') as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br').tag('br')."\n";
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'].'boilerplate/'.$folder;
    }
    $folders[] = boilerplate_data_folder();
    foreach ($folders as $folder) {
	$o .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $o;
}


/**
 * Creates a new boilerplate file, if it doesn't exist. Returns the edit view.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function boilerplate_new($name) {
    if (!boilerplate_valid_name($name)) {
	return boilerplate_admin($name);
    }
    $fn = boilerplate_data_folder().$name.'.dat';
    if (!file_exists($fn)) {
	if (($fh = fopen($fn, 'x')) === FALSE) {
	    e('cntwriteto', 'file', $fn);
	} else {
	    fclose($fh);
	}
    }
    return boilerplate_edit($name);
}


/**
 * Returns the boilerplate edit view.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function boilerplate_edit($name) {
    global $tx, $sn, $cf, $pth;

    $fn = boilerplate_data_folder().$name.'.dat';
    if (($cnt = file_get_contents($fn)) === FALSE) {
	e('cntopen', 'file', $fn);
	return FALSE;
    }
    $url = $sn.'?&amp;boilerplate&amp;admin=plugin_main&amp;action=save&amp;boilerplate_name='.urlencode($name);
    $o = '<div class="plugineditcaption">Boilerplate: '.$name.'</div>'
	    .'<form class="plugineditform" action="'.$url.'" method="POST">'
	    .'<textarea class="plugintextarea" name="boilerplate_text" style="height: '.$cf['editor']['height'].'px">'
		    .htmlspecialchars($cnt).'</textarea>'
	    .(!function_exists('init_editor') || $cf['editor']['external'] == ''
		    ? tag('input type="submit" class="submit" value="'.ucfirst($tx['action']['save']).'"') : '')
	    .'</form>';
    init_editor(array('plugintextarea'));
    return $o;
}


/**
 * Saves the boilerplate. Returns the main administration view.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function boilerplate_save($name) {
    $fn = boilerplate_data_folder().$name.'.dat';
    if (file_exists($fn) && !is_writable($fn)
	    || ($fh = fopen($fn, 'w')) === FALSE
	    || fwrite($fh, stsl($_POST['boilerplate_text'])) === FALSE) {
	e('cntsave', 'file', $fn);
    }
    if (!empty($fh)) {fclose($fh);}
    return boilerplate_admin();
}


/**
 * Deletes the boilerplate $name. Returns the main administration view.
 *
 * @param string $name
 * @return string  The (X)HTML.
 */
function boilerplate_delete($name) {
    $fn = boilerplate_data_folder().$name.'.dat';
    if (!unlink($fn)) {
	e('cntdelete', 'file', $fn);
    }
    return boilerplate_admin();
}


/**
 * Returns the main administration view.
 *
 * @return string  The (X)HTML.
 */
function boilerplate_admin() {
    global $pth, $sn, $tx, $plugin_tx;

    $ptx = $plugin_tx['boilerplate'];
    $o = '<div id="boilerplate_admin" class="plugineditcaption">Boilerplate: '.$ptx['menu_main'].'</div>'."\n"
	    .'<ul>';
    $baseurl = $sn.'?&amp;boilerplate&amp;admin=plugin_main&amp;action=';
    $o .= '<li><form action="'.$baseurl.'new" method="POST">'
	    .tag('input type="text" name="boilerplate_name"')
	    .tag('input type="submit" class="submit" value="'.$ptx['label_create'].'"')
	    .'</form></li>';
    $url = $baseurl.'edit&amp;boilerplate_name=';
    foreach (glob(boilerplate_data_folder().'*.dat') as $file) {
	$name = basename($file, '.dat');
	$o .= '<li>'
		.'<a href="'.$url.urlencode($name).'" title="'.ucfirst($tx['action']['edit']).'" style="float: left">'
			.$name.'</a>'
		.'<form action="'.$baseurl.'delete&amp;boilerplate_name='.urlencode($name).'"'
			.' method="POST" style="float:left; margin-left: 1em">'
		.tag('input type="image" src="'.$pth['folder']['plugins'].'boilerplate/images/delete.png"'
			.' alt="'.ucfirst($tx['action']['delete']).'" title="'.ucfirst($tx['action']['delete']).'"')
		.'</form>'
		.tag('input type="text" readonly="readonly" value="{{{PLUGIN:boilerplate(\''.$name.'\');}}}" onclick="this.select()" style="float: left; margin-left: 1em"')
		.'</li>'."\n";
    }
    $o .= '</ul>'."\n";
    return $o;
}


/**
 * Handle the plugin administration.
 */
if (!empty($boilerplate)) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
	case '':
	    $o .= boilerplate_version().tag('hr').boilerplate_system_check();
	    break;
	case 'plugin_main':
	    switch ($action) {
		case 'new': $o .= boilerplate_new(stsl($_POST['boilerplate_name'])); break;
		case 'edit': $o .= boilerplate_edit(stsl($_GET['boilerplate_name'])); break;
		case 'save': $o .= boilerplate_save(stsl($_GET['boilerplate_name'])); break;
		case 'delete': $o .= boilerplate_delete(stsl($_GET['boilerplate_name'])); break;
		default: $o .= boilerplate_admin();
	    }
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
