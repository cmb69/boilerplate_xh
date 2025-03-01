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
    /** @var string */
    private $folder;

    /** @var array<string,string> */
    private $lang;

    /** @param array<string,string> $lang */
    public function __construct(string $folder, array $lang)
    {
        $this->folder = $folder;
        $this->lang = $lang;
    }

    /** @param array<string,mixed> $_data */
    public function render(string $_template, array $_data): string
    {
        extract($_data);
        ob_start();
        include "{$this->folder}$_template.php";
        return (string) ob_get_clean();
    }

    public function text(string $key): ?string
    {
        return isset($this->lang[$key]) ? XH_hsc($this->lang[$key]) : null;
    }

    /** @param scalar $args */
    public function message(string $kind, string $key, ...$args): string
    {
        return XH_message($kind, $this->lang[$key], ...$args);
    }

    /** @param scalar $args */
    public function error(string $key, ...$args): string
    {
        return XH_message('fail', $this->lang[$key], ...$args);
    }
}
