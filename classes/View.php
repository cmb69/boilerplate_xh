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

class View
{
    /**
     * @var string
     */
    private $template;

    /**
     * @param string $template
     */
    public function __construct($template)
    {
        global $pth;

        $this->template = "{$pth['folder']['plugins']}boilerplate/views/$template.htm";
    }

    /**
     * Renders a template.
     *
     * @param array  $_bag      Variables available in the template.
     *
     * @return string (X)HTML.
     */
    public function render($_bag)
    {
        extract($_bag);
        ob_start();
        include $this->template;
        return ob_get_clean();
    }

    /**
     * @param string $key
     * @return string
     */
    protected function text($key)
    {
        global $plugin_tx;

        return isset($plugin_tx['boilerplate'][$key]) ? XH_hsc($plugin_tx['boilerplate'][$key]) : null;
    }

    /**
     * @return string
     */
    protected function csrfToken()
    {
        global $_XH_csrfProtection;

        return $_XH_csrfProtection->tokenInput();
    }
}
