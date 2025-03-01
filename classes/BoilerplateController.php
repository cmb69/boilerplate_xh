<?php

/**
 * Copyright 2012-2025 Christoph M. Becker
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

class BoilerplateController
{
    /** @var array<string,string> */
    private $lang;

    /** @var TextBlocks */
    private $textBlocks;

    /** @param array<string,string> $lang */
    public function __construct(array $lang, TextBlocks $textBlocks)
    {
        $this->lang = $lang;
        $this->textBlocks = $textBlocks;
    }

    public function __invoke(string $name): string
    {
        if (!$this->textBlocks->isValidName($name)) {
            return XH_message('fail', $this->lang['error_invalid_name'], $name);
        }
        $content = $this->textBlocks->read($name);
        if ($content !== false) {
            return evaluate_scripting($content);
        } else {
            return XH_message('fail', $this->lang['error_cant_read'], $name);
        }
    }
}
