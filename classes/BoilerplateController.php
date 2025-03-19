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

use Boilerplate\Model\TextBlocks;
use Plib\Response;
use Plib\View;

class BoilerplateController
{
    /** @var TextBlocks */
    private $textBlocks;

    /** @var View */
    private $view;

    public function __construct(TextBlocks $textBlocks, View $view)
    {
        $this->textBlocks = $textBlocks;
        $this->view = $view;
    }

    public function __invoke(string $name): Response
    {
        if (!$this->textBlocks->isValidName($name)) {
            return Response::create($this->view->message("fail", "error_invalid_name", $name));
        }
        if (($content = $this->textBlocks->read($name)) === false) {
            return Response::create($this->view->message("fail", "error_cant_read", $name));
        }
        return Response::create($this->evaluateScripting($content));
    }

    protected function evaluateScripting(string $content): string
    {
        return evaluate_scripting($content);
    }
}
