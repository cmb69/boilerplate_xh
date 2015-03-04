<?php

/**
 * Back-end of Boilerplate_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Boilerplate
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
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
 * Returns a text with special characters converted to HTML entities.
 *
 * @param string $text A text.
 *
 * @return string (X)HTML.
 */
function Boilerplate_hsc($text)
{
    if (function_exists('XH_hsc')) {
        return XH_hsc($text);
    } else {
        return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
    }
}

/**
 * Renders a CSRF token input if CSRF protection support is available.
 *
 * @return string (X)HTML.
 *
 * @global XH_CSRFProtection The CSRF protector.
 */
function Boilerplate_renderCsrfTokenInput()
{
    global $_XH_csrfProtection;

    if (isset($_XH_csrfProtection)) {
        return $_XH_csrfProtection->tokenInput();
    } else {
        return '';
    }
}

/**
 * Checks the CSRF token if CSRF protection support is available.
 *
 * @return void
 *
 * @global XH_CSRFProtection The CSRF protector.
 */
function Boilerplate_checkCsrfToken()
{
    global $_XH_csrfProtection;

    if (isset($_XH_csrfProtection)) {
        $_XH_csrfProtection->check();
    }
}

/**
 * Renders a template.
 *
 * @param string $_template The name of the template.
 * @param string $_bag      Variables available in the template.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The configuration of the core.
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
 * @return string (X)HTML.
 *
 * @global array             The paths of system files and folders.
 * @global array             The localization of the core.
 * @global array             The localization of the plugins.
 * @global Boilerplate_Model The model.
 */
function Boilerplate_info() // RELEASE-TODO
{
    global $pth, $tx, $plugin_tx, $_Boilerplate;

    $ptx = $plugin_tx['boilerplate'];
    $labels = array(
        'info' => $ptx['label_info'],
        'syscheck' => $ptx['syscheck_title'],
        'about' => $ptx['about'],
        'logo' => $ptx['alt_logo']
    );
    $labels = array_map('Boilerplate_hsc', $labels);
    $phpVersion = '4.3.0';
    foreach (array('ok', 'warn', 'fail') as $state) {
        $images[$state] = $pth['folder']['plugins']
            . "boilerplate/images/$state.png";
    }
    $checks = array();
    $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
        = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
    foreach (array('pcre') as $ext) {
        $checks[sprintf($ptx['syscheck_extension'], $ext)]
            = extension_loaded($ext) ? 'ok' : 'fail';
    }
    $checks[$ptx['syscheck_magic_quotes']]
        = !get_magic_quotes_runtime() ? 'ok' : 'fail';
    $checks[$ptx['syscheck_encoding']]
        = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
    foreach (array('config/', 'css', 'languages/') as $folder) {
        $folders[] = $pth['folder']['plugins'] . 'boilerplate/' . $folder;
    }
    $folders[] = $_Boilerplate->getDataFolder();
    foreach ($folders as $folder) {
        $checks[sprintf($ptx['syscheck_writable'], $folder)]
            = is_writable($folder) ? 'ok' : 'warn';
    }
    $icon = $pth['folder']['plugins'] . 'boilerplate/boilerplate.png';
    $version = BOILERPLATE_VERSION;
    $bag = compact('labels', 'images', 'checks', 'icon', 'version');
    return Boilerplate_render('info', $bag);
}

/**
 * Creates a new boilerplate file. Redirects to the edit view on success;
 * shows the main administration on failure.
 *
 * @param string $name A boilerplate name.
 *
 * @return string (X)HTML.
 *
 * @global string            Error message to emit in the (X)HTML.
 * @global array             The localization of the plugins.
 * @global Boilerplate_Model The model.
 */
function Boilerplate_new($name)
{
    global $e, $plugin_tx, $_Boilerplate;

    Boilerplate_checkCsrfToken();
    $ptx = $plugin_tx['boilerplate'];
    if (!$_Boilerplate->isValidName($name)) {
        $e .= '<li><b>' . $ptx['error_invalid_name'] . '</b>' . tag('br')
            . $name . '</li>' . PHP_EOL;
        return Boilerplate_admin();
    }
    $fn = $_Boilerplate->filename($name);
    if (!file_exists($fn)) {
        if ($_Boilerplate->write($name, '') !== false) {
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
 * @param string $name    A boilerplate name.
 * @param string $content A boilerplate content.
 *
 * @return string (X)HTML.
 *
 * @global string            The script name.
 * @global array             The paths of system files and folders.
 * @global array             The configuration of the core.
 * @global array             The localization of the core.
 * @global Boilerplate_Model The model.
 */
function Boilerplate_edit($name, $content = null)
{
    global $sn, $pth, $cf, $tx, $_Boilerplate;

    if (!isset($content)) {
        $content = $_Boilerplate->read($name);
        if ($content === false) {
            e('cntopen', 'file', $_Boilerplate->filename($name));
            return false;
        }
    }
    $labels = array(
        'heading' => "Boilerplate \xE2\x80\x93 $name",
        'save' => ucfirst($tx['action']['save'])
    );
    $labels = array_map('Boilerplate_hsc', $labels);
    $url = "$sn?&amp;boilerplate";
    $editorHeight = $cf['editor']['height'];
    $content = Boilerplate_hsc($content);
    $showSubmit = !function_exists('init_editor')
        || $cf['editor']['external'] == '';
    $bag = compact(
        'labels', 'name', 'url', 'editorHeight', 'content', 'showSubmit'
    );
    $o = Boilerplate_render('edit', $bag);
    init_editor(array('plugintextarea'));
    return $o;
}

/**
 * Saves a boilerplate text. Redirects to the main administration on success;
 * returns the edit view on failure.
 *
 * @param string $name A boilerplate.
 *
 * @return string (X)HTML.
 *
 * @global Boilerplate_Model The model.
 */
function Boilerplate_save($name)
{
    global $_Boilerplate;

    Boilerplate_checkCsrfToken();
    $content = stsl($_POST['boilerplate_text']);
    $ok = $_Boilerplate->write($name, $content);
    if ($ok) {
        $qs = '?boilerplate&admin=plugin_main&action=plugin_tx';
        header('Location: ' . BOILERPLATE_URL . $qs, true, 303);
        exit;
    } else {
        e('cntsave', 'file', $_Boilerplate->filename($name));
        return Boilerplate_edit($name, $content);
    }
}

/**
 * Deletes the boilerplate $name. Redirects to the main administration view on
 * success; shows the main administration on failure.
 *
 * @param string $name A boilerplate name.
 *
 * @return string (X)HTML.
 *
 * @global Boilerplate_Model The model.
 */
function Boilerplate_delete($name)
{
    global $_Boilerplate;

    Boilerplate_checkCsrfToken();
    if ($_Boilerplate->delete($name)) {
        $qs = '?boilerplate&admin=plugin_main&action=plugin_tx';
        header('Location: ' . BOILERPLATE_URL . $qs, true, 303);
        exit;
    } else {
        e('cntdelete', 'file', $_Boilerplate->filename($name));
        return Boilerplate_admin();
    }
}

/**
 * Returns the main administration view.
 *
 * @return string (X)HTML.
 *
 * @global string            The script name.
 * @global array             The paths of system files and folders.
 * @global array             The localization of the core.
 * @global array             The localization of the plugins.
 * @global Boilerplate_Model The model.
 */
function Boilerplate_admin()
{
    global $sn, $pth, $tx, $plugin_tx, $_Boilerplate;

    $ptx = $plugin_tx['boilerplate'];
    $labels = array(
        'heading' => "Boilerplate \xE2\x80\x93 $ptx[menu_main]",
        'edit' => ucfirst($tx['action']['edit']),
        'delete' => ucfirst($tx['action']['delete']),
        'create' => $ptx['label_create'],
        'confirm' => addcslashes($ptx['confirm_delete'], "\r\n\\\'")
    );
    $labels = array_map('Boilerplate_hsc', $labels);
    $deleteImage = $pth['folder']['plugins'] . 'boilerplate/images/delete.png';
    $url = $sn . '?&amp;boilerplate';
    $baseURL = $sn . '?&amp;boilerplate&amp;admin=plugin_main&amp;action=';
    $boilerplates = array();
    foreach ($_Boilerplate->names() as $name) {
        $boilerplates[$name] = array(
            'editURL' => $baseURL . 'edit&amp;boilerplate_name=' . $name,
            'deleteURL' => $baseURL . 'delete&amp;boilerplate_name=' . $name
        );
    }
    $bag = compact('labels', 'deleteImage', 'url', 'boilerplates');
    return Boilerplate_render('admin', $bag);
}

/*
 * Register the plugin menu items.
 */
XH_registerStandardPluginMenuItems(true);

/*
 * Handle the plugin administration.
 */
if (function_exists('XH_wantsPluginAdministration')
    && XH_wantsPluginAdministration('boilerplate')
    || isset($boilerplate) && $boilerplate == 'true'
) {
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
        default:
            $o .= Boilerplate_admin();
        }
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
