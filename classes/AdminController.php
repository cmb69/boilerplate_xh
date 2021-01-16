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

use XH\CSRFProtection;

class AdminController
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var CSRFProtection
     */
    private $csrfProtector;

    /**
     * @var View
     */
    private $view;

    public function __construct(Model $model, CSRFProtection $csrfProtector, View $view)
    {
        $this->model = $model;
        $this->csrfProtector = $csrfProtector;
        $this->view = $view;
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
        $phpVersion = '5.6.0';
        foreach (['ok', 'warn', 'fail'] as $state) {
            $images[$state] = $pth['folder']['plugins']
                . "boilerplate/images/$state.png";
        }
        $checks = [];
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (['css', 'languages/'] as $folder) {
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
        return $this->view->render('info', $bag);
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
        $this->csrfProtector->check();
        if (!$this->model->isValidName($name)) {
            return $this->renderError('error_invalid_name', $name)
                . $this->renderMainAdministration();
        }
        $fn = $this->model->filename($name);
        if (!file_exists($fn)) {
            if ($this->model->write($name, '') !== false) {
                $this->relocate("?boilerplate&admin=plugin_main&action=edit&boilerplate_name=$name");
            } else {
                return $this->renderError('error_cant_write', $name)
                    . $this->renderMainAdministration();
            }
        } else {
            return $this->renderError('error_already_exists', $name)
                . $this->renderMainAdministration();
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
                return $this->renderError('error_cant_read', $name)
                    . $this->renderMainAdministration();
            }
        }
        $url = "$sn?&amp;boilerplate";
        $editorHeight = $cf['editor']['height'];
        $content = XH_hsc($content);
        $bag = compact('name', 'url', 'editorHeight', 'content');
        $o = $this->view->render('edit', $bag);
        init_editor(['plugintextarea']);
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
        $this->csrfProtector->check();
        $content = $_POST['boilerplate_text'];
        $ok = $this->model->write($name, $content);
        if ($ok) {
            $this->relocate('?boilerplate&admin=plugin_main&action=plugin_tx');
        } else {
            return $this->renderError('error_cant_write', $name)
                . $this->editTextBlock($name, $content);
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
        $this->csrfProtector->check();
        if ($this->model->delete($name)) {
            $this->relocate('?boilerplate&admin=plugin_main&action=plugin_tx');
        } else {
            return $this->renderError('error_cant_delete', $name)
                . $this->renderMainAdministration();
        }
    }

    /**
     * @param string $url
     */
    private function relocate($url)
    {
        header('Location: ' . CMSIMPLE_URL . $url, true, 303);
        exit;
    }

    /**
     * @param string $msg
     */
    private function renderError($key, ...$args)
    {
        global $plugin_tx;

        return XH_message('fail', $plugin_tx['boilerplate'][$key], ...$args);
    }

    /**
     * Returns the main administration view.
     *
     * @return string (X)HTML.
     */
    public function renderMainAdministration()
    {
        global $sn, $pth, $plugin_tx;

        $deleteImage = $pth['folder']['plugins'] . 'boilerplate/images/delete.png';
        $url = $sn . '?&amp;boilerplate';
        $baseURL = $sn . '?&amp;boilerplate&amp;admin=plugin_main&amp;action=';
        $boilerplates = [];
        foreach ($this->model->names() as $name) {
            $boilerplates[$name] = [
                'editURL' => $baseURL . 'edit&amp;boilerplate_name=' . $name,
                'deleteURL' => $baseURL . 'delete&amp;boilerplate_name=' . $name
            ];
        }
        $bag = compact('deleteImage', 'url', 'boilerplates');
        return $this->renderJsConfigOnce() . $this->view->render('admin', $bag);
    }

    /**
     * @return string
     */
    private function renderJsConfigOnce()
    {
        global $plugin_tx;
        static $done = false;

        if ($done) {
            return '';
        }
        $done = true;
        $config = [
            'delete_confirmation' => $plugin_tx['boilerplate']['confirm_delete'],
        ];
        $json = json_encode($config);
        return "<script>boilerplate = $json;</script>\n";
    }
}
