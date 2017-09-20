<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// Allows you to execute a PHP code before the plugin's database tables and data are dropped during the plugin uninstallation.
require_once(dirname(__FILE__) . '/../../../config.php');
require_once("$CFG->dirroot/mod/mediasite/locallib.php");

defined('MOODLE_INTERNAL') || die();

/**
 * This is called at the beginning of the uninstallation process to give the module
 * a chance to clean-up its hacks, bits etc. where possible.
 *
 * @return bool true if success
 */
function xmldb_mediasite_uninstall() {
    // figure out if the local plugin is installed, it must be removed first
    $localinstalled = is_local_mediasite_courses_installed();
    if ($localinstalled) {
        print_error(get_string('error_local_plugin_still_installed', 'mediasite'));
    }
    return !is_local_mediasite_courses_installed();
}
