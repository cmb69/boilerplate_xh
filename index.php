<?php

/**
 * Front-end of Boilerplate_XH.
 *
 * Copyright (c) 2012-2013 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('BOILERPLATE_VERSION', '1beta1');


if (!function_exists('evaluate_scripting')) {
    function evaluate_scripting($txt) {
	return $txt;
    }
}


/**
 * Returns the data folder.
 *
 * @return string
 */
function boilerplate_data_folder() {
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['boilerplate'];
    if ($pcf['folder_data'] == '') {
	$fn = $pth['folder']['plugins'].'boilerplate/data/';
    } else {
	$fn = $pth['folder']['base'].$pcf['folder_data'];
    }
    if (substr($fn, -1) != '/') {
	$fn .= '/';
    }
    if (file_exists($fn)) {
	if (!is_dir($fn)) {
	    e('cntopen', 'folder', $fn);
	}
    } else {
	if (!mkdir($fn, 0777, TRUE)) {
	    e('cntwriteto', 'folder', $fn);
	}
    }
    return $fn;
}


/**
 * Returns whether the given $name is valid.
 * Emits an error message, if the name is invalid.
 *
 * @param string $name
 * @return bool
 */
function boilerplate_valid_name($name) {
    global $e, $plugin_tx;

    $ptx = $plugin_tx['boilerplate'];
    if (!($valid = preg_match('/^[a-z0-9_\-]*$/su', $name))) {
	$e .= '<li><b>'.$ptx['error_invalid_name'].'</b>'.tag('br').$name.'</li>'."\n";
    }
    return $valid;
}


/**
 * Returns the text block.
 *
 * @access public
 * @param string $name
 * @return string  The (X)HTML.
 */
function boilerplate($name) {
    if (!boilerplate_valid_name($name)) {
	return FALSE;
    }
    $fn = boilerplate_data_folder().$name.'.dat';
    if (is_readable($fn) && ($cnt = file_get_contents($fn)) !== FALSE) {
	return evaluate_scripting($cnt);
    } else {
	e('cntopen', 'file', $fn);
	return FALSE;
    }
}

?>
