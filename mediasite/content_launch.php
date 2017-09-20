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

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/mediasite/basiclti_locallib.php');
require_once($CFG->dirroot.'/mod/mediasite/basiclti_mediasite_lib.php');
require_once("$CFG->dirroot/mod/mediasite/locallib.php");
require_once("$CFG->dirroot/mod/mediasite/mediasiteresource.php");
require_once("$CFG->dirroot/mod/mediasite/exceptions.php");

global $CFG, $DB;

$id       = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a        = optional_param('a', 0, PARAM_INT);  // mediasite ID
$frameset = optional_param('frameset', '', PARAM_ALPHA);
$inpopup  = optional_param('inpopup', 0, PARAM_BOOL);
$coverplay = optional_param('coverplay', 0, PARAM_BOOL);

if ($id) {
    if (! ($cm = $DB->get_record("course_modules", array("id" => $id)))) {
         print_error(get_string('error_course_module_id_incorrect', 'mediasite'));
     }
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error(get_string('error_course_misconfigured', 'mediasite'));
}

if (! ($mediasite = $DB->get_record("mediasite", array("id" => $cm->instance)))) {
    print_error(get_string('error_course_module_incorrect', 'mediasite'));
} else {
    if (! ($course = $DB->get_record("course", array("id" => $mediasite->course)))) {
        print_error(get_string('error_course_misconfigured', 'mediasite'));
    }
    if (! ($cm = get_coursemodule_from_instance("mediasite", $mediasite->id, $course->id))) {
        print_error(get_string('error_course_module_id_incorrect', 'mediasite'));
    }
}

require_login($course);

$endpoint = $coverplay ? mediasite_endpoint::LTI_COVERPLAY : mediasite_endpoint::LTI_LAUNCH;

basiclti_mediasite_view($course, $mediasite->siteid, $endpoint, $mediasite->resourceid);

