<?php

/**
 * Copyright 2012-2021 Christoph M. Becker
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

use XH\CSRFProtection;

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * @var string $action
 * @var string $admin
 * @var string $o
 * @var array<string,array<string,string>> $plugin_tx
 * @var array{folder:array<string,string>,file:array<string,string>} $pth
 * @var XH\CSRFProtection $_XH_csrfProtection
 */

XH_registerStandardPluginMenuItems(true);

if (XH_wantsPluginAdministration('boilerplate')) {
    $o .= print_plugin_admin('on');
    $temp = new Boilerplate\AdminController(
        new Boilerplate\TextBlocks("{$pth['folder']['base']}content/boilerplate/"),
        $_XH_csrfProtection,
        new Boilerplate\View($plugin_tx["boilerplate"], $_XH_csrfProtection)
    );
    switch ($admin) {
        case '':
            $o .= $temp->renderInfo();
            break;
        case 'plugin_main':
            switch ($action) {
                case 'new':
                    $o .= $temp->newTextBlock($_POST['boilerplate_name']);
                    break;
                case 'edit':
                    $o .= $temp->editTextBlock($_GET['boilerplate_name']);
                    break;
                case 'save':
                    $o .= $temp->saveTextBlock($_POST['boilerplate_name']);
                    break;
                case 'delete':
                    $o .= $temp->deleteTextBlock($_POST['boilerplate_name']);
                    break;
                default:
                    $o .= $temp->renderMainAdministration();
            }
            break;
        default:
            $o .= plugin_admin_common();
    }
}
