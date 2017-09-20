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

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$siteid = required_param('site', PARAM_INT);          // site
$context = context_system::instance();

require_login();
require_capability('mod/mediasite:addinstance', $context);

global $DB;
$site = $DB->get_field('mediasite_sites', 'id', array('id' => $siteid));
$defaultId = $DB->get_field('mediasite_config', 'siteid', array());

// if $site isn't found, then the site doesn't exist, bail out
if ($site == null || !isset($site)) {
    redirect("configuration.php");
}

// find the id of the module
$moduleid = $DB->get_field('modules', 'id', array('name' => 'mediasite'));

// delete content records associated with this site
$DB->delete_records_select('course_modules', 'module = '.$moduleid.' AND instance IN (SELECT id FROM {mediasite} M WHERE siteid = '.$site.' AND M.course = {course_modules}.course)', null);
$DB->delete_records('mediasite', array('siteid' => $site));

// delete course configuration associated with this site
$DB->delete_records('mediasite_course_config', array('mediasite_site' => $site));

//Check if the server to be deleted is the default one.
//If so, set the first server in table 'mediasite_sites' to be default.
if ($defaultId == $siteid) {
    $DB->delete_records('mediasite_sites', array('id'=>$siteid));
    $DB->delete_records('mediasite_config', array('siteid'=>$siteid));
    
    $sites = array_values($DB->get_records('mediasite_sites', array()));
    if($sites == null) 
        redirect("configuration.php");
    $site = $sites[0];  
    $record = new \stdClass();
    $record->siteid = $site->id;
    $record->openaspopup = 1;
    $DB->insert_record('mediasite_config', $record);
} else {
    $DB->delete_records('mediasite_sites', array('id'=>$siteid));
}
// Go home
redirect("configuration.php");