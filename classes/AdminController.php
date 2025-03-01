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
    /** @var TextBlocks */
    private $model;

    /** @var CSRFProtection */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function __construct(TextBlocks $model, CSRFProtection $csrfProtector, View $view)
    {
        $this->model = $model;
        $this->csrfProtector = $csrfProtector;
        $this->view = $view;
    }

    /** @return string|void */
    public function newTextBlock(string $name)
    {
        $this->csrfProtector->check();
        if (!$this->model->isValidName($name)) {
            return $this->view->error('error_invalid_name', $name)
                . $this->renderMainAdministration();
        }
        if (!$this->model->exists($name)) {
            if ($this->model->write($name, '') !== false) {
                $this->relocate("?boilerplate&admin=plugin_main&action=edit&boilerplate_name=$name");
            } else {
                return $this->view->error('error_cant_write', $name)
                    . $this->renderMainAdministration();
            }
        } else {
            return $this->view->error('error_already_exists', $name)
                . $this->renderMainAdministration();
        }
    }

    public function editTextBlock(string $name, ?string $content = null): string
    {
        global $sn, $cf;

        if (!isset($content)) {
            $content = $this->model->read($name);
            if ($content === false) {
                return $this->view->error('error_cant_read', $name)
                    . $this->renderMainAdministration();
            }
        }
        $o = $this->view->render('edit', [
            "name" => $name,
            "url" => "$sn?&amp;boilerplate",
            "editorHeight" => $cf['editor']['height'],
            "content" => XH_hsc($content),
            "csrf_token_input" => $this->csrfProtector->tokenInput(),
        ]);
        init_editor(['plugintextarea']);
        return $o;
    }

    /** @return string|void */
    public function saveTextBlock(string $name)
    {
        $this->csrfProtector->check();
        $content = $_POST['boilerplate_text'];
        $ok = $this->model->write($name, $content);
        if ($ok) {
            $this->relocate('?boilerplate&admin=plugin_main&action=plugin_tx');
        } else {
            return $this->view->error('error_cant_write', $name)
                . $this->editTextBlock($name, $content);
        }
    }

    /** @return string|void */
    public function deleteTextBlock(string $name)
    {
        $this->csrfProtector->check();
        if ($this->model->delete($name)) {
            $this->relocate('?boilerplate&admin=plugin_main&action=plugin_tx');
        } else {
            return $this->view->error('error_cant_delete', $name)
                . $this->renderMainAdministration();
        }
    }

    /** @return never */
    private function relocate(string $url)
    {
        header('Location: ' . CMSIMPLE_URL . $url, true, 303);
        exit;
    }

    public function renderMainAdministration(): string
    {
        global $sn;

        $baseURL = $sn . '?&amp;boilerplate&amp;admin=plugin_main&amp;action=';
        $boilerplates = [];
        foreach ($this->model->names() as $name) {
            $boilerplates[$name] = [
                'editURL' => $baseURL . 'edit&amp;boilerplate_name=' . $name,
                'deleteURL' => $baseURL . 'delete&amp;boilerplate_name=' . $name
            ];
        }
        return $this->renderJsConfigOnce() . $this->view->render('admin', [
            "url" => $sn . '?&amp;boilerplate',
            "boilerplates" => $boilerplates,
            "csrf_token_input" => $this->csrfProtector->tokenInput(),
        ]);
    }

    private function renderJsConfigOnce(): string
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
