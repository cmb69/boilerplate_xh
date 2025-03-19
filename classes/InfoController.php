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
use Plib\SystemChecker;
use Plib\View;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var TextBlocks */
    private $model;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, TextBlocks $model, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->model = $model;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function __invoke(): Response
    {
        $phpVersion = '7.1.0';
        $checks = [];
        $checks[] = $this->view->message(
            $this->systemChecker->checkVersion(PHP_VERSION, $phpVersion) ? 'success' : 'fail',
            'syscheck_phpversion',
            $phpVersion
        );
        $xhVersion = "1.7.0";
        $checks[] = $this->view->message(
            $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $xhVersion") ? 'success' : 'fail',
            'syscheck_xhversion',
            $xhVersion
        );
        $plibVersion = "1.2";
        $checks[] = $this->view->message(
            $this->systemChecker->checkPlugin("plib", $plibVersion) ? 'success' : 'fail',
            'syscheck_plibversion',
            $plibVersion
        );
        foreach (['css', 'languages/'] as $folder) {
            $folders[] = $this->pluginFolder . $folder;
        }
        $folders[] = $this->model->getDataFolder();
        foreach ($folders as $folder) {
            $checks[] = $this->view->message(
                $this->systemChecker->checkWritability($folder) ? 'success' : 'warning',
                'syscheck_writable',
                $folder
            );
        }
        return Response::create($this->view->render('info', [
            "checks" => $checks,
            "version" => BOILERPLATE_VERSION,
        ]));
    }
}
