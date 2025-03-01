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

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var TextBlocks */
    private $model;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, TextBlocks $model, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->model = $model;
        $this->view = $view;
    }

    public function renderInfo(): string
    {
        $phpVersion = '7.1.0';
        $checks = [];
        $checks[] = $this->view->message(
            version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'success' : 'fail',
            'syscheck_phpversion',
            $phpVersion
        );
        foreach (['css', 'languages/'] as $folder) {
            $folders[] = $this->pluginFolder . $folder;
        }
        $folders[] = $this->model->getDataFolder();
        foreach ($folders as $folder) {
            $checks[] = $this->view->message(
                is_writable($folder) ? 'success' : 'warning',
                'syscheck_writable',
                $folder
            );
        }
        return $this->view->render('info', [
            "checks" => $checks,
            "version" => BOILERPLATE_VERSION,
        ]);
    }
}
