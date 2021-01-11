<?php

/**
 * Copyright 2013-2021 Christoph M. Becker
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

namespace Boilerplate;

class AdminController
{
    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Returns the plugin information view.
     *
     * @return string (X)HTML.
     */
    public function renderInfo()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['boilerplate'];
        $phpVersion = '5.4.0';
        foreach (array('ok', 'warn', 'fail') as $state) {
            $images[$state] = $pth['folder']['plugins']
                . "boilerplate/images/$state.png";
        }
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array('css', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'boilerplate/' . $folder;
        }
        $folders[] = $this->model->getDataFolder();
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        $icon = $pth['folder']['plugins'] . 'boilerplate/boilerplate.png';
        $version = BOILERPLATE_VERSION;
        $bag = compact('images', 'checks', 'icon', 'version');
        return (new View('info'))->render($bag);
    }

    /**
     * Creates a new boilerplate file. Redirects to the edit view on success;
     * shows the main administration on failure.
     *
     * @param string $name A boilerplate name.
     *
     * @return string (X)HTML.
     */
    public function newTextBlock($name)
    {
        global $e, $plugin_tx, $_XH_csrfProtection;

        $_XH_csrfProtection->check();
        $ptx = $plugin_tx['boilerplate'];
        if (!$this->model->isValidName($name)) {
            $e .= '<li><b>' . $ptx['error_invalid_name'] . '</b><br>'
                . $name . '</li>' . PHP_EOL;
            return $this->renderMainAdministration();
        }
        $fn = $this->model->filename($name);
        if (!file_exists($fn)) {
            if ($this->model->write($name, '') !== false) {
                $qs = '?boilerplate&admin=plugin_main&action=edit&boilerplate_name='
                    . $name;
                header('Location: ' . CMSIMPLE_URL . $qs, true, 303);
                exit;
            } else {
                e('cntwriteto', 'file', $fn);
                return $this->renderMainAdministration();
            }
        } else {
            e('alreadyexists', 'file', $fn);
            return $this->renderMainAdministration();
        }
    }

    /**
     * Returns the boilerplate edit view.
     *
     * @param string $name    A boilerplate name.
     * @param string $content A boilerplate content.
     *
     * @return string (X)HTML.
     */
    public function editTextBlock($name, $content = null)
    {
        global $sn, $pth, $cf;

        if (!isset($content)) {
            $content = $this->model->read($name);
            if ($content === false) {
                e('cntopen', 'file', $this->model->filename($name));
                return false;
            }
        }
        $url = "$sn?&amp;boilerplate";
        $editorHeight = $cf['editor']['height'];
        $content = XH_hsc($content);
        $bag = compact('name', 'url', 'editorHeight', 'content');
        $o = (new View('edit'))->render($bag);
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
     */
    public function saveTextBlock($name)
    {
        global $_XH_csrfProtection;

        $_XH_csrfProtection->check();
        $content = $_POST['boilerplate_text'];
        $ok = $this->model->write($name, $content);
        if ($ok) {
            $qs = '?boilerplate&admin=plugin_main&action=plugin_tx';
            header('Location: ' . CMSIMPLE_URL . $qs, true, 303);
            exit;
        } else {
            e('cntsave', 'file', $this->model->filename($name));
            return $this->editTextBlock($name, $content);
        }
    }

    /**
     * Deletes the boilerplate $name. Redirects to the main administration view on
     * success; shows the main administration on failure.
     *
     * @param string $name A boilerplate name.
     *
     * @return string (X)HTML.
     */
    public function deleteTextBlock($name)
    {
        global $_XH_csrfProtection;

        $_XH_csrfProtection->check();
        if ($this->model->delete($name)) {
            $qs = '?boilerplate&admin=plugin_main&action=plugin_tx';
            header('Location: ' . CMSIMPLE_URL . $qs, true, 303);
            exit;
        } else {
            e('cntdelete', 'file', $this->model->filename($name));
            return $this->renderMainAdministration();
        }
    }

    /**
     * Returns the main administration view.
     *
     * @return string (X)HTML.
     */
    public function renderMainAdministration()
    {
        global $sn, $pth, $plugin_tx;

        $confirmation = XH_hsc(addcslashes($plugin_tx['boilerplate']['confirm_delete'], "\r\n\\\'"));
        $deleteImage = $pth['folder']['plugins'] . 'boilerplate/images/delete.png';
        $url = $sn . '?&amp;boilerplate';
        $baseURL = $sn . '?&amp;boilerplate&amp;admin=plugin_main&amp;action=';
        $boilerplates = array();
        foreach ($this->model->names() as $name) {
            $boilerplates[$name] = array(
                'editURL' => $baseURL . 'edit&amp;boilerplate_name=' . $name,
                'deleteURL' => $baseURL . 'delete&amp;boilerplate_name=' . $name
            );
        }
        $bag = compact('confirmation', 'deleteImage', 'url', 'boilerplates');
        return (new View('admin'))->render($bag);
    }
}
