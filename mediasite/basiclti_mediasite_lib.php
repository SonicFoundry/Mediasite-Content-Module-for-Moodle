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

require_once($CFG->dirroot."/config.php");
require_once($CFG->dirroot.'/mod/mediasite/basiclti_locallib.php');

defined('MOODLE_INTERNAL') || die();

class mediasite_endpoint {
    const LTI_SEARCH = 0;
    const LTI_MY_MEDIASITE = 1;
    const LTI_CATALOG = 2;
    const LTI_LAUNCH = 3;
    const LTI_COVERPLAY = 4;
}

class mediasite_menu_placement {
    const SITE_PAGES = 0;
    const COURSE_MENU = 1;
}

function basiclti_mediasite_view($instance, $siteid, $endpointtype, $mediasiteid = null) {
    global $USER;

    $typeconfig = basiclti_get_type_config($siteid);
    $endpoint = $typeconfig->endpoint;
    $roles = get_all_enrollments();

    if (!isset($typeconfig->lti_consumer_key)) {
        $inpopup = optional_param('inpopup', 0, PARAM_BOOL);
        redirect(new moodle_url('/mod/mediasite/error.php', array('inpopup' => $inpopup)));
    }

    switch ($endpointtype) {
        case mediasite_endpoint::LTI_SEARCH:
            $endpoint = $endpoint.'/LTI/';
            break;
        case mediasite_endpoint::LTI_MY_MEDIASITE:
            $endpoint = $endpoint.'/LTI/MyMediasite';
            break;
        case mediasite_endpoint::LTI_CATALOG:
            $endpoint = $endpoint.'/LTI/Catalog';
            break;
        case mediasite_endpoint::LTI_LAUNCH:
            if ($mediasiteid == null) {
                throw new moodle_exception('generalexceptionmessage', 'error', '', 'basiclti_mediasite_view was called without a value for $mediasiteid.');
            }
            $endpoint = $endpoint.'/LTI/Home/Launch?mediasiteid='.$mediasiteid;
            break;
        case mediasite_endpoint::LTI_COVERPLAY:
            if ($mediasiteid == null) {
                throw new moodle_exception('generalexceptionmessage', 'error', '', 'basiclti_mediasite_view was called without a value for $mediasiteid.');
            }
            $endpoint = $endpoint.'/LTI/Home/Coverplay?mediasiteid='.$mediasiteid;
            break;
        default:
            throw new moodle_exception('generalexceptionmessage', 'error', '', 'basiclti_mediasite_view was called with an invalid value for the $endpointtype argument.');
    }

    basiclti_view($instance, $siteid, $typeconfig, $endpoint, $roles);
}

function get_all_enrollments() {
    global $DB, $USER;

    $allenrollments = array();

    $selectenrolledcourses = '
        SELECT DISTINCT c.id, c.shortname, c.idnumber
          FROM {user} u
               INNER JOIN {role_assignments} ra ON ra.userid = u.id
               INNER JOIN {context} ct ON ct.id = ra.contextid
               INNER JOIN {course} c ON c.id = ct.instanceid
         WHERE u.id = ?';
    $courseids = $DB->get_records_sql($selectenrolledcourses, array($USER->id));

    foreach ($courseids as $courseId) {
        // context_course::instance($course->id);
        $context = context_course::instance($courseId->id);
        // function basiclti_get_ims_role($user, $context)
        $role = basiclti_get_ims_role($USER, $context);
        array_push($allenrollments, get_mediasite_formatted_role($role, $courseId->shortname));
    }
    return $allenrollments;
}

function get_mediasite_formatted_role($imsrole, $courseidentifier) {
    return $courseidentifier.':'.$imsrole;
}