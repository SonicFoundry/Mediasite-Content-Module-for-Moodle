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

/**
 * Mediasite plugin for Moodle.
 *
 * @package mod_mediasite
 * @copyright Sonic Foundry 2017  {@link http://sonicfoundry.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/mod/mediasite/lib.php");
require_once("$CFG->dirroot/mod/mediasite/mediasitesite.php");
require_once("$CFG->dirroot/mod/mediasite/mediasiteresource.php");

define("MEDIASITE_MOODLE_TIMEOUT", 25);

function mediasite_is_local_mediasite_courses_installed() {
    // Extend settings for each local plugin. Note that their settings may be in any part of the
    // settings tree and may be visible not only for administrators.
    foreach (core_plugin_manager::instance()->get_plugins_of_type('local') as $plugin) {
        if (strcmp($plugin->component, 'local_mediasite_courses') === 0) {
            return true;
        }
    }
    return false;
}

function mediasite_check_resource_permission($resourceid, $resourcetype, $username) {
    return true;
}

function mediasite_get_editor_options($context) {
    global $CFG;
    return array(
        'subdirs' => 1,
        'maxbytes' => $CFG->maxbytes,
        'maxfiles' => -1,
        'changeformat' => 1,
        'context' => $context,
        'noclean' => 1,
        'trusttext' => 0
    );
}

function mediasite_has_value($value) {
    return isset($value) && !is_null($value) && trim($value) != '';
}
