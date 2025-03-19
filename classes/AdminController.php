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
use Plib\Url;
use Plib\View;
use XH\CSRFProtection;

class AdminController
{
    /** @var string */
    private $editorHeight;

    /** @var TextBlocks */
    private $model;

    /** @var CSRFProtection */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function __construct(
        string $editorHeight,
        TextBlocks $model,
        CSRFProtection $csrfProtector,
        View $view
    ) {
        $this->editorHeight = $editorHeight;
        $this->model = $model;
        $this->csrfProtector = $csrfProtector;
        $this->view = $view;
    }

    public function __invoke(Request $request, string $action): Response
    {
        switch ($action) {
            case 'new':
                return $this->newTextBlock($request, $request->post("boilerplate_name") ?? "");
            case 'edit':
                return $this->editTextBlock($request, $request->get("boilerplate_name") ?? "");
            case 'save':
                return $this->saveTextBlock($request, $request->post("boilerplate_name") ?? "");
            case 'delete':
                return $this->deleteTextBlock($request, $request->post("boilerplate_name") ?? "");
            default:
                return Response::create($this->renderMainAdministration($request->url()));
        }
    }

    private function newTextBlock(Request $request, string $name): Response
    {
        $this->csrfProtector->check();
        if (!$this->model->isValidName($name)) {
            return Response::create($this->view->message("fail", 'error_invalid_name', $name)
                . $this->renderMainAdministration($request->url()));
        }
        if ($this->model->exists($name)) {
            return Response::create($this->view->message("fail", 'error_already_exists', $name)
                . $this->renderMainAdministration($request->url()));
        }
        if (!$this->model->write($name, '')) {
            return Response::create($this->view->message("fail", 'error_cant_write', $name)
                . $this->renderMainAdministration($request->url()));
        }
        $url = $request->url()->with("admin", "plugin_main")->with("action", "edit")->with("boilerplate_name", $name);
        return Response::redirect($url->absolute());
    }

    private function editTextBlock(Request $request, string $name, ?string $content = null): Response
    {
        if (!isset($content)) {
            if (($content = $this->model->read($name)) === false) {
                return Response::create($this->view->message("fail", 'error_cant_read', $name)
                    . $this->renderMainAdministration($request->url()));
            }
        }
        $o = $this->renderEditor($request, $name, $content);
        return Response::create($o);
    }

    private function renderEditor(Request $request, string $name, string $content): string
    {
        $o = $this->view->render('edit', [
            "name" => $name,
            "url" => $request->url()->without("action")->relative(),
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
                . $this->renderEditor($request, $name, $content));
        }
        return Response::redirect($request->url()->with("action", "plugin_tx")->absolute());
    }

    private function deleteTextBlock(Request $request, string $name): Response
    {
        $this->csrfProtector->check();
        if (!$this->model->delete($name)) {
            return Response::create($this->view->message("fail", 'error_cant_delete', $name)
                . $this->renderMainAdministration($request->url()));
        }
        return Response::redirect(
            $request->url()->with("admin", "plugin_main")->with("action", "plugin_tx")->absolute()
        );
    }

    private function renderMainAdministration(Url $url): string
    {
        $boilerplates = [];
        foreach ($this->model->names() as $name) {
            $boilerplates[$name] = [
                'editURL' => $url->with("action", "edit")->with("boilerplate_name", $name)->relative(),
                'deleteURL' => $url->with("action", "delete")->with("boilerplate_name", $name)->relative(),
            ];
        }
        return $this->view->render('admin', [
            "url" => $url->without("admin")->without("action")->relative(),
            "boilerplates" => $boilerplates,
            "csrf_token_input" => $this->csrfProtector->tokenInput(),
        ]);
    }
}
