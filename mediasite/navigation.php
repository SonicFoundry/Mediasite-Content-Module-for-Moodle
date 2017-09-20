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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/mediasite/basiclti_mediasite_lib.php');

function mediasite_extend_navigation_user_settings(navigation_node $parentnode, stdClass $user, context_user $context, stdClass $course, context_course $coursecontext) {
}

function mediasite_navigation_extension_mymediasite() {
    global $PAGE;
    debugging('mediasite_navigation_extension_mymediasite');
    $mediasitenode = $PAGE->navigation->add(get_string('mediasite', 'mediasite'), null, navigation_node::TYPE_COURSE);    
    $url = new moodle_url('/mod/mediasite/mymediasite.php');
    $mymediasitenode = $mediasitenode->add(get_string('my_mediasite', 'mediasite'), $url);
    $mymediasitenode->make_active();
    return $mediasitenode;
}

function mediasite_extend_navigation_course_settings(navigation_node $parentnode, context_course $context) {
    $OVERRIDE_CAPABILITY = 'mod/mediasite:overridedefaults';
    if (!has_capability($OVERRIDE_CAPABILITY, $context)) {
        return;
    }
    global $PAGE;
    $label = get_string('course_settings', 'mediasite');
    $key = 'mediasite_course_settings';
    $coursesettings = $parentnode->get($key);
    if ($coursesettings == null && $PAGE->course->id > 1) {
        $coursesettings = $parentnode->add($label, new moodle_url('/mod/mediasite/site/course_settings.php', array('id' => $PAGE->course->id)), navigation_node::TYPE_SETTING, null, $key, new pix_icon('i/settings', $label));
    }
    return $coursesettings;
}

function mediasite_navigation_extension_mymediasite_placement() {
    if (!isLoggedIn()) {
        return;
    }

    global $PAGE, $USER, $DB;

    if (empty($USER->id)) {
        return;
    }

    $SITE_PAGES_ID = 1;
    $id = optional_param('id', $SITE_PAGES_ID, PARAM_INT);
    if (($PAGE->course != null)) {
        // BUG44083:    MOODLE: Naviagation error in Moodle if MyMediasite clicked while viewing media
        // debugging('using $PAGE->course->id ('.$PAGE->course->id.') instead of $id ('.$id.').');
        $id = $PAGE->course->id;
    }
    $MY_MEDIASITE_CAPABILITY = 'mod/mediasite:mymediasite';
    $mymediasiteplacements = get_mediasite_sites(false, true);
    // $sitepagesnode = $PAGE->navigation->find($SITE_PAGES_ID, navigation_node::TYPE_COURSE);
    $sitepagesnode = $PAGE->navigation->get('home');
    $coursenode = null;
    $coursecontext = null;
    $coursemediasitesite = null;
    $usercontext = context_user::instance($USER->id);

    if ($PAGE->course != null && $PAGE->course->id != $SITE_PAGES_ID) {
        $coursecontext = context_course::instance($PAGE->course->id);
        $coursenode = $PAGE->navigation->find($PAGE->course->id, navigation_node::TYPE_COURSE);
        $coursemediasitesite = $DB->get_field('mediasite_course_config', 'mediasite_site', array('course' => $PAGE->course->id));
    }

    $showboostdivider = true;

    foreach ($mymediasiteplacements as $site) {
        $url = new moodle_url('/mod/mediasite/mymediasite.php', array('id' => $id, 'siteid'=>$site->id));
        // debugging('mediasite_navigation_extension_mymediasite_placement: ' . $site->my_mediasite_placement . ' title: ' . $site->my_mediasite_title);
        switch ($site->my_mediasite_placement) {
            case mediasite_menu_placement::SITE_PAGES:
                // debugging('SITE_PAGES $site->id: '.$site->id.' : $site->my_mediasite_title: '.$site->my_mediasite_title.' : has_capability($MY_MEDIASITE_CAPABILITY, $usercontext): '.has_capability($MY_MEDIASITE_CAPABILITY, $usercontext));
                if (has_capability($MY_MEDIASITE_CAPABILITY, $usercontext)) {
                    // debugging('show my mediasite is_boost_navigation_available = ' . is_boost_navigation_available());
                    $sitepagesnode->add($site->my_mediasite_title, $url);
                    if (is_boost_navigation_available()) {
                        // add to Boost Navigation
                        add_to_boost_navigation($site->my_mediasite_title, $url, $usercontext, $showboostdivider);
                        $showboostdivider = false;
                    }
                }
                break;
            case mediasite_menu_placement::COURSE_MENU:
                if (1 > 1) {
                    if (is_null($coursecontext) || $coursecontext == null) {
                        debugging('coursecontext is null');
                    }
                    if (is_null($coursenode) || $coursenode == null) {
                        debugging('coursenode is null');
                    }
                    // if (is_null($coursemediasitesite) || $coursemediasitesite == null) {
                    //     debugging('coursemediasitesite is null');
                    // }
                    // if (!($coursenode == $site->id)) {
                    //     debugging('wrong site id coursenode = ' . $coursenode . ', site->id = ' . $site->id);
                    // }
                    if (!has_capability($MY_MEDIASITE_CAPABILITY, $coursecontext)) {
                        debugging('missing MyMediasite capability');
                    }
                }
                if ($coursecontext != null && $coursenode != null && has_capability($MY_MEDIASITE_CAPABILITY, $coursecontext)) {
                    // debugging('course menu placement');
                    $coursenode->add($site->my_mediasite_title, $url);
                }
                if (is_boost_navigation_available()) {
                    // debugging('course menu boost placement');
                    add_to_boost_navigation($site->my_mediasite_title, $url, $usercontext, $showboostdivider);
                }
                break;
            default:
                debugging('The value for my_mediasite_placement in mediasite_navigation_extension_mymediasite_placement is not valid. The value was '.$site->get_my_mediasite_placement().'.');
        }
    }
}

function is_boost_navigation_available() {
    global $PAGE, $CFG;
    $reason = 'All tests passed.';
    $result = true;
    // debugging('pagetype: ' . $PAGE->pagetype);
    try {
        if (!isLoggedIn()) {
            $reason = 'is_boost_navigation_available found you are not logged in';
            $result = false;
        }
        else if ($CFG->version < 2016120500) {
            $reason = 'is_boost_navigation_available  detected this is pre-3.2.';
            $result = false;
        }
        else if (is_null($PAGE)) {
            $reason = 'is_boost_navigation_available $PAGE is null';
            $result = false;
        }
        else if (starts_with($PAGE->pagetype, 'admin-')) {
            // On admin pages the $PAGE object acts weird, don't add Mediasite to admin pages.
            $reason = 'is_boost_navigation_available pagetype contains admin: ' . $PAGE->pagetype;
            $result = false;
        }
        else if (starts_with($PAGE->pagetype, 'grade-report-')) {
            // On admin pages the $PAGE object acts weird, don't add Mediasite to admin pages.
            $reason = 'is_boost_navigation_available pagetype contains grade-report: ' . $PAGE->pagetype;
            $result = false;
        }
        else if ($PAGE->pagetype == 'course-view-topics') {
            $reason = 'is_boost_navigation_available COURSE VIEW TOPICS';
            $result = false;
        }
        else if ($PAGE->pagetype == 'course-user') {
            $reason = 'is_boost_navigation_available COURSE USER';
            $result = false;
        }
        else if ($PAGE->pagetype == 'notes-index') {
            $reason = 'is_boost_navigation_available NOTES INDEX';
            $result = false;
        }
        // else if (!property_exists($PAGE, 'settingsnav')) {
        //     $reason = 'is_boost_navigation_available SETTINGSNAV is not defined';
        //     $result = false;
        // }
        else if (is_null($PAGE->settingsnav)) {
            $reason = 'is_boost_navigation_available SETTINGSNAV is null';
            $result = false;
        }
        else if (is_null($PAGE->navigation)) {
            $reason = 'is_boost_navigation_available NAVIGATION is null';
            $result = false;
        }
        else if (is_null($PAGE->flatnav)) {
            $reason = 'is_boost_navigation_available FLATNAV is null';
            $result = false;
        }
    } catch (Exception $e) {
        $reason = $e->getMessage();
    }
    if (!$result) {
        // debugging('is_boost_navigation_available: ' . $result . ' -- ' . $reason);
    }
    return $result;
}

function add_to_boost_navigation($linkText, $url, $context, $showboostdivider) {
    global $PAGE;
    // debugging('adding to boost: ' . $linkText);
    try {
        $dashboard = navigation_node::create($linkText, $url);
        $flat = new flat_navigation_node($dashboard, 0);
        $flat->set_showdivider($showboostdivider);
        $flat->key = rand(1000,9999);
        $PAGE->flatnav->add($flat);
        $templatecontext['flatnavigation'] = $PAGE->flatnav;
    } catch (Exception $e) {
        debugging('add_to_boost_navigation broke, ' . $e->getMessage());
    }
}

function starts_with($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function mediasite_navigation_extension_courses7_course() {
    
    global $PAGE, $DB;
    $course = $PAGE->course;

    if ($course && $course->id > 1) {
        $context = context_course::instance($course->id);
        if (!has_capability('mod/mediasite:courses7', $context)) {
            // blowup('user does not have mediasite:courses7 capability');
            return;
        }

        $coursenode = $PAGE->navigation->find($course->id, navigation_node::TYPE_COURSE);

        try {
            $courseconfig = $DB->get_record('mediasite_course_config', array('course' => $course->id), '*', MUST_EXIST);

            // debugging('is_numeric:'.is_numeric($courseconfig->mediasite_courses_enabled).' $courseconfig->mediasite_courses_enabled: '.$courseconfig->mediasite_courses_enabled.' $courseconfig->mediasite_courses_enabled > 1: "'.($courseconfig->mediasite_courses_enabled > 1).'" is_bool($courseconfig->mediasite_courses_enabled > 1): '.is_bool($courseconfig->mediasite_courses_enabled > 1));
            // debugging('$courseconfig :: ' . is_null($courseconfig) . ' :: ' . isset($courseconfig));

            if ($courseconfig->mediasite_courses_enabled) {
                $site = new Sonicfoundry\MediasiteSite($courseconfig->mediasite_site);
                // debugging('mediasite_navigation_extension_courses7_course: (2) adding ' . $site->get_integration_catalog_title());
                $url = new moodle_url('/mod/mediasite/courses7.php', array('id'=>$course->id, 'siteid'=>$courseconfig->mediasite_site));
                $coursesnode = $coursenode->add($site->get_integration_catalog_title(), $url);
                if (is_boost_navigation_available()) {
                    add_to_boost_navigation($site->get_integration_catalog_title(), $url, $context, false);
                }
            } else {
                // debugging('mediasite_navigation_extension_courses7_course: (3) not adding course nav');
            }
        } catch (Exception $e) {
            foreach (get_mediasite_sites(true, false) as $site) {
                // debugging('mediasite_navigation_extension_courses7_course: (1) adding ' . $site->integration_catalog_title);
                $url = new moodle_url('/mod/mediasite/courses7.php', array('id'=>$course->id, 'siteid'=>$site->id));
                $coursesnode = $coursenode->add($site->integration_catalog_title, $url);
                if (is_boost_navigation_available()) {
                    add_to_boost_navigation($site->integration_catalog_title, $url, $context, false);
                }
            }
        }
    }
}

function get_mediasite_sites($onlyintegrationcatalogenabled = false, $onlymymediasiteenabled = false){
    global $DB;
    $select = '';
    $sort = '';
    if ($onlyintegrationcatalogenabled && $onlymymediasiteenabled) {
        $select = 'show_integration_catalog > 1 AND show_my_mediasite = 1';
    }
    else if ($onlyintegrationcatalogenabled) {
        $select = 'show_integration_catalog > 1';
        $sort = 'integration_catalog_title';
    } else if ($onlymymediasiteenabled) {
        $select = 'show_my_mediasite = 1';
        $sort = 'my_mediasite_title';
    }
    return $DB->get_records_select('mediasite_sites', $select, null, $sort, '*');
}

function blowup($msg) {
    //throw new moodle_exception('generalexceptionmessage', 'error', '', $msg);    
    debugging($msg);
}
