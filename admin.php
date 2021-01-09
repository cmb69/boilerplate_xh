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

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/*
 * Register the plugin menu items.
 */
XH_registerStandardPluginMenuItems(true);

/*
 * Handle the plugin administration.
 */
if (XH_wantsPluginAdministration('boilerplate')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $o .= (new Boilerplate\AdminController)->renderInfo();
            break;
        case 'plugin_main':
            switch ($action) {
                case 'new':
                    $o .= (new Boilerplate\AdminController)->newTextBlock($_POST['boilerplate_name']);
                    break;
                case 'edit':
                    $o .= (new Boilerplate\AdminController)->editTextBlock($_GET['boilerplate_name']);
                    break;
                case 'save':
                    $o .= (new Boilerplate\AdminController)->saveTextBlock($_POST['boilerplate_name']);
                    break;
                case 'delete':
                    $o .= (new Boilerplate\AdminController)->deleteTextBlock($_POST['boilerplate_name']);
                    break;
                default:
                    $o .= (new Boilerplate\AdminController)->renderMainAdministration();
            }
            break;
        default:
            $o .= plugin_admin_common($action, $admin, $plugin);
    }
}
