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
    /**
     * @var CSRFProtection
     */
    private $csrfProtector;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $data;

    public function __construct(CSRFProtection $csrfProtector)
    {
        $this->csrfProtector = $csrfProtector;
    }

    /**
     * Renders a template.
     *
     * @param string $template
     * @param array  $data
     *
     * @return string (X)HTML.
     */
    public function render($template, $data)
    {
        global $pth;

        $this->template = "{$pth['folder']['plugins']}boilerplate/views/$template.php";
        $this->data = $data;
        return $this->doRender();
    }

    /**
     * @return string
     */
    private function doRender()
    {
        extract($this->data);
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
        return $this->csrfProtector->tokenInput();
    }
}
