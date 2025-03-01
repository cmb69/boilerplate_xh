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

class View
{
    /** @var array<string,string> */
    private $lang;

    /** @var CSRFProtection */
    private $csrfProtector;

    /** @var string */
    private $template;

    /** @var array<string,mixed> */
    private $data;

    /** @param array<string,string> $lang */
    public function __construct(array $lang, CSRFProtection $csrfProtector)
    {
        $this->lang = $lang;
        $this->csrfProtector = $csrfProtector;
    }

    /** @param array<string,mixed> $data */
    public function render(string $template, array $data): string
    {
        global $pth;

        $this->template = "{$pth['folder']['plugins']}boilerplate/views/$template.php";
        $this->data = $data;
        return $this->doRender();
    }

    private function doRender(): string
    {
        extract($this->data);
        ob_start();
        include $this->template;
        return (string) ob_get_clean();
    }

    protected function text(string $key): ?string
    {
        return isset($this->lang[$key]) ? XH_hsc($this->lang[$key]) : null;
    }

    /** @param scalar $args */
    public function error(string $key, ...$args): string
    {
        return XH_message('fail', $this->lang[$key], ...$args);
    }

    protected function csrfToken(): string
    {
        return $this->csrfProtector->tokenInput();
    }
}
