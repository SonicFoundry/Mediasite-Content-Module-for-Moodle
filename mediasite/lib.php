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

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../config.php');
require_once("$CFG->dirroot/mod/mediasite/navigation.php");
require_once("$CFG->dirroot/lib/formslib.php");
require_once("$CFG->dirroot/mod/mediasite/mediasitesite.php");
require_once("$CFG->dirroot/mod/mediasite/exceptions.php");

function mediasite_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        case FEATURE_ADVANCED_GRADING:        return true;
        case FEATURE_PLAGIARISM:              return true;

        default: return null;
    }
}

function mediasite_add_instance($data, $mform = null) {
    global $DB,$CFG;
    require_once("$CFG->libdir/resourcelib.php");

    $cmid = $data->coursemodule;

    $data->id = $DB->insert_record('mediasite', $data);

    return $data->id;
}
function mediasite_update_instance($data, $mform) {
    global $DB, $CFG;
    require_once("$CFG->libdir/resourcelib.php");

    $data->id    = $data->instance;

    $DB->update_record('mediasite', $data);

    return true;
}
function mediasite_delete_instance($mediasiteId) {
    global $DB;
    return $DB->delete_records("mediasite", array('id'=>$mediasiteId));
}
/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return cached_cm_info|null
 */
function mediasite_get_coursemodule_info($coursemodule) {
    global $DB;

    if ($mediasite = $DB->get_record('mediasite', array('id'=>$coursemodule->instance), 'id, course, name, description, resourceid, resourcetype, FROM_UNIXTIME(recorddateutc) AS recorddate, recorddateutc, presenters, LENGTH(LTRIM(RTRIM(presenters))) AS presenters_length, sofotags, LENGTH(LTRIM(RTRIM(sofotags))) AS tags_length, displaymode, launchurl, siteid')) {

        $lti = $DB->get_record('mediasite_sites', array('id' => $mediasite->siteid), $fields='*', $strictness=MUST_EXIST);
        // on upgrade, the lti consumer key must be set to allow a successful post. Only show this if the configuration is valid
        if (isset($lti->lti_consumer_key)) {
            if (empty($mediasite->name)) {
                // mediasite name missing, fix it
                $mediasite->name = "label{$mediasite->id}";
                $DB->set_field('mediasite', 'name', $mediasite->name, array('id'=>$mediasite->id));
            }
            if(!$record = $DB->get_record("mediasite_sites", array('id' => $mediasite->siteid))) {
                mediasite_delete_instance($mediasite->id);
                return null;
            }

            $info = new cached_cm_info();
            // $info->content = format_module_intro('mediasite', $mediasite, $coursemodule->id, false);
            $info->content = render_mediasite_resource($mediasite, $coursemodule, $lti);

            return $info;
        } else {
            return null;
        }

    } else {
        return null;
    }
}
function mediasite_user_complete($mediasite) {

}
function mediasite_user_outline($mediasite) {

}
function mediasite_cron($mediasite) {

}
function mediasite_print_recent_activity($mediasite) {
}

function render_mediasite_resource($mediasite, $coursemodule, $lti) {
    if ($mediasite->displaymode == 'BasicLTI') {
       return;
    }

    if ($mediasite->resourcetype == 'Presentation') {
        switch ($mediasite->displaymode) {
            case 'MetadataLight' :
            case 'MetadataOnly' :
                return render_mediasite_presentation_metadata($mediasite, $coursemodule, $lti);
            break;
            case 'MetadataPlusPlayer' :
                $content = render_mediasite_presentation_metadata($mediasite, $coursemodule, $lti);
                $content .= generate_mediasite_iframe($mediasite, $coursemodule);
                return $content;
            break;
            case 'iFrame' :
                $content = generate_mediasite_iframe($mediasite, $coursemodule);
                return $content;
            break;
            case 'PlayerOnly' :
                $content = generate_mediasite_iframe($mediasite, $coursemodule);
                return $content;
            break;
            case 'PresentationLink' :
                return '';
            break;
            default :
                return render_mediasite_presentation_metadata($mediasite, $coursemodule, $lti);
        }
    } else {
        switch ($mediasite->displaymode) {
            case 'iFrame' :
                $content = render_mediasite_catalog_metadata($mediasite, $coursemodule->showdescription);
                $content .= generate_mediasite_iframe($mediasite, $coursemodule);
                return $content;
            break;
            default :
                return render_mediasite_catalog_metadata($mediasite, $coursemodule->showdescription);
        }
    }
}

function render_mediasite_catalog_metadata($mediasite, $showdescription) {
    if (!($showdescription)) {
        return null;
    }
    $content = html_writer::start_tag('div', array('class' => 'sofo-detail')); // 1
    if (isset($mediasite->description) && !is_null($mediasite->description)) {
        $content .= html_writer::tag('div', $mediasite->description, array('class' => 'mediasite-description')); // 2\

    }
    $content .= html_writer::end_tag('div'); // 1
    return $content;
}

function render_mediasite_presentation_metadata($mediasite, $coursemodule, $lti) {
    $showdescription = $coursemodule->showdescription;
    if (!($showdescription)) {
        return null;
    }

    global $CFG;

    $lightMode = ($mediasite->displaymode == 'MetadataLight');
    $hideThumbnail = ($mediasite->displaymode == 'MetadataPlusPlayer');

    $content = html_writer::start_tag('div', array('class' => 'sofo-detail')); // 1

    if (!$hideThumbnail) {
        $content .= html_writer::tag('img', '', array('align' => 'right',
                                                      'class' => 'mediasite-thumbnail',
                                                      'onerror' => 'this.style.display="none"',
                                                      'onload' => 'this.style.display="block"',
                                                      'onclick' => 'window.location.href="'.$CFG->wwwroot.'/mod/mediasite/view.php?id='.$coursemodule->id.'";',
                                                      'src' => generate_mediasite_presentation_thumbnail_url($mediasite, $lti))); // 2
    }
    if (isset($mediasite->recorddateutc) && !is_null($mediasite->recorddateutc)) {
        $content .= html_writer::tag('span', userdate($mediasite->recorddateutc, get_string('strftimedate')), array('class' => 'mediasite-air-date')); // 3
    }
    if (!$lightMode && isset($mediasite->description) && !is_null($mediasite->description)) {
        $content .= html_writer::tag('div', $mediasite->description, array('class' => 'mediasite-description')); // 4
    }
    if (!$lightMode) {
        $content .= generate_mediasite_presenters($mediasite); // 5
        $content .= generate_mediasite_tags($mediasite); // 6
    }
    $content .= html_writer::end_tag('div'); // 1

    return $content;
}

function generate_mediasite_presenters($mediasite) {
    $content = '';
    if (isset($mediasite->presenters) && !is_null($mediasite->presenters) && $mediasite->presenters_length > 0) {
        // split on the delimiter ~!~
        $presenters = explode('~!~', $mediasite->presenters);
        if (count($presenters) > 0) {
            $content = html_writer::tag('div', get_string('presenters', 'mediasite'), array('class' => 'mediasite-presenter-header'));
            $content .= html_writer::start_tag('ul', array('class' => 'mediasite-presenter-list')); // 1
            foreach ($presenters as $presenter) {
                $content .= html_writer::tag('li', $presenter, array('class' => 'mediasite-presenter')); // 2
            }
            $content .= html_writer::end_tag('ul'); // 1
        }
    }
    return $content;
}

function generate_mediasite_tags($mediasite) {
    $content = '';
    if (isset($mediasite->sofotags) && !is_null($mediasite->sofotags) && $mediasite->tags_length > 0) {
        // split on the delimiter ~!~
        $tags = explode('~!~', $mediasite->sofotags);
        if (count($tags) > 0) {
            $content = html_writer::tag('div', get_string('sofotags', 'mediasite'), array('class' => 'mediasite-tags-header'));
            $content .= html_writer::tag('div', implode(', ', $tags), array('class' => 'mediasite-tags'));
        }
    }
    return $content;
}

function generate_mediasite_presentation_thumbnail_url($mediasite, $lti) {
    return $lti->endpoint.'/LTI/Thumbnail?id='.$mediasite->resourceid.'&tck='.base64_encode(mb_convert_encoding($lti->lti_consumer_key, 'UTF-16LE', 'UTF-8')).'&height=168&width=300';
}

function generate_mediasite_iframe($mediasite, $coursemodule) {
    $coverplay = $mediasite->resourcetype == 'Presentation' ? '1' : '0';
    $css = 'mediasite-content-iframe';
    if ($mediasite->resourcetype == 'CatalogFolderDetails') {
        $css .= ' mediasite-content-iframe-catalog';
    }
    $url = new moodle_url('/mod/mediasite/content_launch.php', array('id' => $coursemodule->id, 'coverplay' => $coverplay));
    $content = html_writer::tag('iframe', null, array('id' => 'mod-mediasite-view-'.$coursemodule->id, 'class' => $css, 'src' => $url));
    return $content;
}

function mediasite_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    return mediasite_extend_navigation_course_settings($parentnode, $context);
}

