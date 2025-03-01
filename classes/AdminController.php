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
    /** @var string */
    private $scriptName;

    /** @var string */
    private $editorHeight;

    /** @var TextBlocks */
    private $model;

    /** @var CSRFProtection */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function __construct(
        string $scriptName,
        string $editorHeight,
        TextBlocks $model,
        CSRFProtection $csrfProtector,
        View $view
    ) {
        $this->scriptName = $scriptName;
        $this->editorHeight = $editorHeight;
        $this->model = $model;
        $this->csrfProtector = $csrfProtector;
        $this->view = $view;
    }

    /** @return string|never */
    public function __invoke(string $action)
    {
        switch ($action) {
            case 'new':
                return $this->newTextBlock($_POST['boilerplate_name']);
            case 'edit':
                return $this->editTextBlock($_GET['boilerplate_name']);
            case 'save':
                return $this->saveTextBlock($_POST['boilerplate_name']);
            case 'delete':
                return $this->deleteTextBlock($_POST['boilerplate_name']);
            default:
                return $this->renderMainAdministration();
        }
    }

    /** @return string|never */
    private function newTextBlock(string $name)
    {
        $this->csrfProtector->check();
        if (!$this->model->isValidName($name)) {
            return $this->view->error('error_invalid_name', $name)
                . $this->renderMainAdministration();
        }
        if ($this->model->exists($name)) {
            return $this->view->error('error_already_exists', $name)
                . $this->renderMainAdministration();
        }
        if ($this->model->write($name, '') === false) {
            return $this->view->error('error_cant_write', $name)
                . $this->renderMainAdministration();
        }
        $this->relocate("?boilerplate&admin=plugin_main&action=edit&boilerplate_name=$name");
    }

    private function editTextBlock(string $name, ?string $content = null): string
    {
        if (!isset($content)) {
            if (($content = $this->model->read($name)) === false) {
                return $this->view->error('error_cant_read', $name)
                    . $this->renderMainAdministration();
            }
        }
        $o = $this->view->render('edit', [
            "name" => $name,
            "url" => "{$this->scriptName}?&amp;boilerplate",
            "editorHeight" => $this->editorHeight,
            "content" => XH_hsc($content),
            "csrf_token_input" => $this->csrfProtector->tokenInput(),
        ]);
        $this->initEditor();
        return $o;
    }

    /** @return void */
    protected function initEditor()
    {
        init_editor(['plugintextarea']);
    }

    /** @return string|never */
    private function saveTextBlock(string $name)
    {
        $this->csrfProtector->check();
        $content = $_POST['boilerplate_text'];
        if (!$this->model->write($name, $content)) {
            return $this->view->error('error_cant_write', $name)
                . $this->editTextBlock($name, $content);
        }
        $this->relocate('?boilerplate&admin=plugin_main&action=plugin_tx');
    }

    /** @return string|never */
    private function deleteTextBlock(string $name)
    {
        $this->csrfProtector->check();
        if (!$this->model->delete($name)) {
            return $this->view->error('error_cant_delete', $name)
                . $this->renderMainAdministration();
        }
        $this->relocate('?boilerplate&admin=plugin_main&action=plugin_tx');
    }

    /** @return never */
    protected function relocate(string $url)
    {
        header('Location: ' . CMSIMPLE_URL . $url, true, 303);
        exit;
    }

    private function renderMainAdministration(): string
    {
        $baseURL = $this->scriptName . '?&amp;boilerplate&amp;admin=plugin_main&amp;action=';
        $boilerplates = [];
        foreach ($this->model->names() as $name) {
            $boilerplates[$name] = [
                'editURL' => $baseURL . 'edit&amp;boilerplate_name=' . $name,
                'deleteURL' => $baseURL . 'delete&amp;boilerplate_name=' . $name
            ];
        }
        return $this->view->render('admin', [
            "url" => $this->scriptName . '?&amp;boilerplate',
            "boilerplates" => $boilerplates,
            "csrf_token_input" => $this->csrfProtector->tokenInput(),
        ]);
    }
}
