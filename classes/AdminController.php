<?php

/**
 * Copyright (c) Christoph M. Becker
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

use Plib\Request;
use Plib\Response;
use Plib\View;
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

    public function __invoke(Request $request, string $action): Response
    {
        switch ($action) {
            case 'new':
                return $this->newTextBlock($request->post("boilerplate_name") ?? "");
            case 'edit':
                return $this->editTextBlock($request->get("boilerplate_name") ?? "");
            case 'save':
                return $this->saveTextBlock($request, $request->post("boilerplate_name") ?? "");
            case 'delete':
                return $this->deleteTextBlock($request->post("boilerplate_name") ?? "");
            default:
                return Response::create($this->renderMainAdministration());
        }
    }

    private function newTextBlock(string $name): Response
    {
        $this->csrfProtector->check();
        if (!$this->model->isValidName($name)) {
            return Response::create($this->view->message("fail", 'error_invalid_name', $name)
                . $this->renderMainAdministration());
        }
        if ($this->model->exists($name)) {
            return Response::create($this->view->message("fail", 'error_already_exists', $name)
                . $this->renderMainAdministration());
        }
        if ($this->model->write($name, '') === false) {
            return Response::create($this->view->message("fail", 'error_cant_write', $name)
                . $this->renderMainAdministration());
        }
        return Response::redirect(CMSIMPLE_URL . "?boilerplate&admin=plugin_main&action=edit&boilerplate_name=$name");
    }

    private function editTextBlock(string $name, ?string $content = null): Response
    {
        if (!isset($content)) {
            if (($content = $this->model->read($name)) === false) {
                return Response::create($this->view->message("fail", 'error_cant_read', $name)
                    . $this->renderMainAdministration());
            }
        }
        $o = $this->renderEditor($name, $content);
        return Response::create($o);
    }

    private function renderEditor(string $name, string $content): string
    {
        $o = $this->view->render('edit', [
            "name" => $name,
            "url" =>  "{$this->scriptName}?&amp;boilerplate",
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

    private function saveTextBlock(Request $request, string $name): Response
    {
        $this->csrfProtector->check();
        $content = $request->post("boilerplate_text") ?? "";
        if (!$this->model->write($name, $content)) {
            return Response::create($this->view->message("fail", 'error_cant_write', $name)
                . $this->renderEditor($name, $content));
        }
        return Response::redirect(CMSIMPLE_URL . '?boilerplate&admin=plugin_main&action=plugin_tx');
    }

    private function deleteTextBlock(string $name): Response
    {
        $this->csrfProtector->check();
        if (!$this->model->delete($name)) {
            return Response::create($this->view->message("fail", 'error_cant_delete', $name)
                . $this->renderMainAdministration());
        }
        return Response::redirect(CMSIMPLE_URL . '?boilerplate&admin=plugin_main&action=plugin_tx');
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
