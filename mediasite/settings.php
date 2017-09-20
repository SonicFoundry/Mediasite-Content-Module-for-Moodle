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

require_once(dirname(__FILE__) . '/../../config.php');
require_once("$CFG->dirroot/mod/mediasite/lib.php");
require_once("$CFG->dirroot/mod/mediasite/locallib.php");

defined('MOODLE_INTERNAL') || die();

$modsettingmediasiteurl = new moodle_url('/mod/mediasite/site/configuration.php?section=modmediasite');

if (strpos(strtolower($_SERVER["REQUEST_URI"]), 'mediasite/site/configuration.php')
    || strpos(strtolower($_SERVER["REQUEST_URI"]), 'mediasite/site/add.php')
    || strpos(strtolower($_SERVER["REQUEST_URI"]), 'mediasite/site/edit.php')) {

    $settings = new admin_externalpage('activitysettingmediasite',
        get_string('pluginname', 'mediasite'),
        $modsettingmediasiteurl,
        'mod/mediasite:addinstance');

} else {
    $settings->add(new admin_setting_heading('name', get_string('admin_settings_header', 'mediasite'), get_string('admin_settings_body', 'mediasite', $CFG->wwwroot.'/mod/mediasite/site/configuration.php?section=modmediasite').'<script type="text/javascript">window.location.href="'.$modsettingmediasiteurl.'";</script>'));
    // redirect($modsettingmediasiteurl);
}


