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
    /** @var TextBlocks */
    private $model;

    /** @var View */
    private $view;

    public function __construct(TextBlocks $model, View $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function renderInfo(): string
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['boilerplate'];
        $phpVersion = '7.1.0';
        foreach (['ok', 'warn', 'fail'] as $state) {
            $images[$state] = $pth['folder']['plugins']
                . "boilerplate/images/$state.png";
        }
        $checks = [];
        $checks[] = XH_message(
            version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'success' : 'fail',
            $ptx['syscheck_phpversion'],
            $phpVersion
        );
        $checks[] = XH_message(
            extension_loaded('json') ? 'success' : 'fail',
            $ptx['syscheck_extension'],
            'JSON'
        );
        foreach (['css', 'languages/'] as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'boilerplate/' . $folder;
        }
        $folders[] = $this->model->getDataFolder();
        foreach ($folders as $folder) {
            $checks[] = XH_message(
                is_writable($folder) ? 'success' : 'warning',
                $ptx['syscheck_writable'],
                $folder
            );
        }
        return $this->view->render('info', [
            "images" => $images,
            "checks" => $checks,
            "version" => BOILERPLATE_VERSION,
        ]);
    }
}
