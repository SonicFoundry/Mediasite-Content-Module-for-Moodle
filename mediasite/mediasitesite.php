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

namespace Sonicfoundry;

defined('MOODLE_INTERNAL') || die();

class Mediasitecontenttypes {
    const PRESENTATION = 'Presentation';
    const CATALOG = 'CatalogFolderDetails';
}
class MediasiteEmbedFormatValues {
    const THUMBNAIL = 1;
    const ABSTRACT_ONLY = 2;
    const ABSTRACT_PLUS_PLAYER = 4;
    const LINK = 8;
    const EMBED = 16;
    const PRESENTATION_LINK = 32;
    const PLAYER_ONLY = 64;
}
class MediasiteEmbedFormatTypes {
    const THUMBNAIL = 'MetadataLight';
    const ABSTRACT_ONLY = 'MetadataOnly';
    const ABSTRACT_PLUS_PLAYER = 'MetadataPlusPlayer';
    const LINK = 'BasicLTI';
    const EMBED = 'iFrame';
    const PRESENTATION_LINK = 'PresentationLink';
    const PLAYER_ONLY = 'PlayerOnly';
}
class MediasiteEmbedFormat {
    public $contenttype;
    public $formatvalue;
    public $formattype;
    public $enabled;

    function __construct($contenttype = null, $formatvalue = null, $formattype = null, $enabled = null) {
        $this->contenttype = $contenttype;
        $this->formatvalue = $formatvalue;
        $this->formattype = $formattype;
        $this->enabled = $enabled;
    }
}


/**
 * Class MediasiteSite
 * @package Sonicfoundry
 */
class MediasiteSite {
    private $id;
    private $sitename;
    private $endpoint;
    private $lti_consumer_key;
    private $lti_consumer_secret;
    private $lti_custom_parameters;
    private $show_integration_catalog;
    private $integration_catalog_title;
    private $openpopup_integration_catalog;
    private $show_my_mediasite;
    private $my_mediasite_title;
    private $lti_debug_launch;
    private $my_mediasite_placement;
    private $openaspopup_my_mediasite;
    private $embed_formats;

    function __construct($record = null) {
        if(!is_null($record)) {
            if($record instanceof MediasiteSite) {
                $this->id = $record->id;
                $this->sitename = $record->sitename;
                $this->endpoint = $record->endpoint;
                $this->lti_consumer_key = $record->lti_consumer_key;
                $this->lti_consumer_secret = $record->lti_consumer_secret;
                $this->lti_custom_parameters = $record->lti_custom_parameters;
                $this->embed_formats = $record->embed_formats;
                $this->show_integration_catalog = $record->show_integration_catalog;
                $this->integration_catalog_title = $record->integration_catalog_title;
                $this->openpopup_integration_catalog = $record->openpopup_integration_catalog;
                $this->show_my_mediasite = $record->show_my_mediasite;
                $this->my_mediasite_title = $record->my_mediasite_title;
                $this->my_mediasite_placement = $record->my_mediasite_placement;
                $this->openaspopup_my_mediasite = $record->openaspopup_my_mediasite;
                $this->lti_debug_launch = $record->lti_debug_launch;
            } else if($record instanceof \stdClass) {
                $this->id = $record->id;
                $this->sitename = $record->sitename;
                $this->endpoint = $record->endpoint;
                $this->lti_consumer_key = $record->lti_consumer_key;
                $this->lti_consumer_secret = $record->lti_consumer_secret;
                $this->lti_custom_parameters = $record->lti_custom_parameters;
                $this->embed_formats = $record->embed_formats;
                $this->show_integration_catalog = $record->show_integration_catalog;
                $this->integration_catalog_title = $record->integration_catalog_title;
                $this->openpopup_integration_catalog = $record->openpopup_integration_catalog;
                $this->show_my_mediasite = $record->show_my_mediasite;
                $this->my_mediasite_title = $record->my_mediasite_title;
                $this->my_mediasite_placement = $record->my_mediasite_placement;
                $this->openaspopup_my_mediasite = $record->openaspopup_my_mediasite;
                $this->lti_debug_launch = $record->lti_debug_launch;
            } else if(is_numeric($record)) {
                global $DB;
                $record = $DB->get_record('mediasite_sites', array('id'=>$record));
                if($record) {
                    $this->id = $record->id;
                    $this->sitename = $record->sitename;
                    $this->endpoint = $record->endpoint;
                    $this->lti_consumer_key = $record->lti_consumer_key;
                    $this->lti_consumer_secret = $record->lti_consumer_secret;
                    $this->lti_custom_parameters = $record->lti_custom_parameters;
                    $this->embed_formats = $record->embed_formats;
                    $this->show_integration_catalog = $record->show_integration_catalog;
                    $this->integration_catalog_title = $record->integration_catalog_title;
                    $this->openpopup_integration_catalog = $record->openpopup_integration_catalog;
                    $this->show_my_mediasite = $record->show_my_mediasite;
                    $this->my_mediasite_title = $record->my_mediasite_title;
                    $this->my_mediasite_placement = $record->my_mediasite_placement;
                    $this->openaspopup_my_mediasite = $record->openaspopup_my_mediasite;
                    $this->lti_debug_launch = $record->lti_debug_launch;
                }
            }
        }
    }
    function update_database() {
        $record = new \stdClass();
        $record->id = $this->id;
        $record->sitename = $this->sitename;
        $record->endpoint = $this->endpoint;
        $record->lti_consumer_key = $this->lti_consumer_key;
        $record->lti_consumer_secret = $this->lti_consumer_secret;
        $record->lti_custom_parameters = $this->lti_custom_parameters;
        $record->embed_formats = $this->embed_formats;
        $record->show_integration_catalog = $this->show_integration_catalog;
        $record->integration_catalog_title = $this->integration_catalog_title;
        $record->openpopup_integration_catalog = $this->openpopup_integration_catalog;
        $record->show_my_mediasite = $this->show_my_mediasite;
        $record->my_mediasite_title = $this->my_mediasite_title;
        $record->my_mediasite_placement = $this->my_mediasite_placement;
        $record->openaspopup_my_mediasite = $this->openaspopup_my_mediasite;
        $record->lti_debug_launch = $this->lti_debug_launch;

        global $DB;
        $DB->update_record('mediasite_sites', $record);
    }
    public function get_embed_capabilities($includeDisabled = false, $contenttypeFilter = null) {
        // based on $embed_formats, return an array of MediasiteEmbedFormats
        $result = array();
        if (($includeDisabled || $this->embed_formats & MediasiteEmbedFormatValues::PRESENTATION_LINK) && ($contenttypeFilter == null || $contenttypeFilter == Mediasitecontenttypes::PRESENTATION)) {
            $result[] = new MediasiteEmbedFormat(Mediasitecontenttypes::PRESENTATION, MediasiteEmbedFormatValues::PRESENTATION_LINK, MediasiteEmbedFormatTypes::PRESENTATION_LINK, $this->embed_formats & MediasiteEmbedFormatValues::PRESENTATION_LINK);
        }
        if (($includeDisabled || $this->embed_formats & MediasiteEmbedFormatValues::THUMBNAIL) && ($contenttypeFilter == null || $contenttypeFilter == Mediasitecontenttypes::PRESENTATION)) {
            $result[] = new MediasiteEmbedFormat(Mediasitecontenttypes::PRESENTATION, MediasiteEmbedFormatValues::THUMBNAIL, MediasiteEmbedFormatTypes::THUMBNAIL, $this->embed_formats & MediasiteEmbedFormatValues::THUMBNAIL);
        }
        if (($includeDisabled || $this->embed_formats & MediasiteEmbedFormatValues::ABSTRACT_ONLY) && ($contenttypeFilter == null || $contenttypeFilter == Mediasitecontenttypes::PRESENTATION)) {
            $result[] = new MediasiteEmbedFormat(Mediasitecontenttypes::PRESENTATION, MediasiteEmbedFormatValues::ABSTRACT_ONLY, MediasiteEmbedFormatTypes::ABSTRACT_ONLY, $this->embed_formats & MediasiteEmbedFormatValues::ABSTRACT_ONLY);
        }
        if (($includeDisabled || $this->embed_formats & MediasiteEmbedFormatValues::PLAYER_ONLY) && ($contenttypeFilter == null || $contenttypeFilter == Mediasitecontenttypes::PRESENTATION)) {
            $result[] = new MediasiteEmbedFormat(Mediasitecontenttypes::PRESENTATION, MediasiteEmbedFormatValues::PLAYER_ONLY, MediasiteEmbedFormatTypes::PLAYER_ONLY, $this->embed_formats & MediasiteEmbedFormatValues::PLAYER_ONLY);
        }
        if (($includeDisabled || $this->embed_formats & MediasiteEmbedFormatValues::ABSTRACT_PLUS_PLAYER) && ($contenttypeFilter == null || $contenttypeFilter == Mediasitecontenttypes::PRESENTATION)) {
            $result[] = new MediasiteEmbedFormat(Mediasitecontenttypes::PRESENTATION, MediasiteEmbedFormatValues::ABSTRACT_PLUS_PLAYER, MediasiteEmbedFormatTypes::ABSTRACT_PLUS_PLAYER, $this->embed_formats & MediasiteEmbedFormatValues::ABSTRACT_PLUS_PLAYER);
        }
        if (($includeDisabled || $this->embed_formats & MediasiteEmbedFormatValues::LINK) && ($contenttypeFilter == null || $contenttypeFilter == Mediasitecontenttypes::CATALOG)) {
            $result[] = new MediasiteEmbedFormat(Mediasitecontenttypes::CATALOG, MediasiteEmbedFormatValues::LINK, MediasiteEmbedFormatTypes::LINK, $this->embed_formats & MediasiteEmbedFormatValues::LINK);
        }
        if (($includeDisabled || $this->embed_formats & MediasiteEmbedFormatValues::EMBED) && ($contenttypeFilter == null || $contenttypeFilter == Mediasitecontenttypes::CATALOG)) {
            $result[] = new MediasiteEmbedFormat(Mediasitecontenttypes::CATALOG, MediasiteEmbedFormatValues::EMBED, MediasiteEmbedFormatTypes::EMBED, $this->embed_formats & MediasiteEmbedFormatValues::EMBED);
        }
        return $result;
    }
    public function get_siteid() {
        return $this->id;
    }
    public function set_sitename($value) {
        $this->sitename = $value;
    }
    public function get_sitename() {
        return $this->sitename;
    }
    public function set_endpoint($value) {
        $this->endpoint = $value;
    }
    public function get_endpoint() {
        return $this->endpoint;
    }
    public function set_lti_consumer_key($value) {
        $this->lti_consumer_key = $value;
    }
    public function get_lti_consumer_key() {
        return $this->lti_consumer_key;
    }
    public function set_lti_consumer_secret($value) {
        $this->lti_consumer_secret = $value;
    }
    public function get_lti_consumer_secret() {
        return $this->lti_consumer_secret;
    }
    public function set_lti_custom_parameters($value) {
        $this->lti_custom_parameters = $value;
    }
    public function get_lti_custom_parameters() {
        return $this->lti_custom_parameters;
    }
    public function set_show_integration_catalog($value) {
        $this->show_integration_catalog = $value;
    }
    public function get_show_integration_catalog() {
        return $this->show_integration_catalog;
    }
    public function set_integration_catalog_title($value) {
        $this->integration_catalog_title = $value;
    }
    public function get_integration_catalog_title() {
        return $this->integration_catalog_title;
    }
    public function get_openpopup_integration_catalog() {
        return $this->openpopup_integration_catalog;
    }
    public function set_openpopup_integration_catalog($value) {
        $this->openpopup_integration_catalog = $value;
    }
    public function set_show_my_mediasite($value) {
        $this->show_my_mediasite = $value;
    }
    public function get_show_my_mediasite() {
        return $this->show_my_mediasite;
    }
    public function set_my_mediasite_title($value) {
        $this->my_mediasite_title = $value;
    }
    public function get_my_mediasite_title() {
        return $this->my_mediasite_title;
    }
    public function set_my_mediasite_placement($value) {
        $this->my_mediasite_placement = $value;
    }
    public function get_my_mediasite_placement() {
        return $this->my_mediasite_placement;
    }
    public function get_openaspopup_my_mediasite() {
        return $this->openaspopup_my_mediasite;
    }
    public function set_openaspopup_my_mediasite($value) {
        $this->openaspopup_my_mediasite = $value;
    }
    public function set_lti_debug_launch($value) {
        $this->lti_debug_launch = $value;
    }
    public function get_lti_debug_launch() {
        return $this->lti_debug_launch;
    }
    public function get_lti_embed_type_thumbnail() {
        return $this->embed_formats & MediasiteEmbedFormatValues::THUMBNAIL;
    }
    public function set_lti_embed_type_thumbnail($value) {
        $this->set_lti_embed_type_bitmask($value, MediasiteEmbedFormatValues::THUMBNAIL);
    }
    public function get_lti_embed_type_abstract_only() {
        return $this->embed_formats & MediasiteEmbedFormatValues::ABSTRACT_ONLY;
    }
    public function set_lti_embed_type_abstract_only($value) {
        $this->set_lti_embed_type_bitmask($value, MediasiteEmbedFormatValues::ABSTRACT_ONLY);
    }
    public function get_lti_embed_type_abstract_plus_player() {
        return $this->embed_formats & MediasiteEmbedFormatValues::ABSTRACT_PLUS_PLAYER;
    }
    public function set_lti_embed_type_abstract_plus_player($value) {
        $this->set_lti_embed_type_bitmask($value, MediasiteEmbedFormatValues::ABSTRACT_PLUS_PLAYER);
    }
    public function get_lti_embed_type_link() {
        return $this->embed_formats & MediasiteEmbedFormatValues::LINK;
    }
    public function set_lti_embed_type_link($value) {
        $this->set_lti_embed_type_bitmask($value, MediasiteEmbedFormatValues::LINK);
    }
    public function get_lti_embed_type_embed() {
        return $this->embed_formats & MediasiteEmbedFormatValues::EMBED;
    }
    public function set_lti_embed_type_embed($value) {
        $this->set_lti_embed_type_bitmask($value, MediasiteEmbedFormatValues::EMBED);
    }
    public function get_lti_embed_type_presentation_link() {
        return $this->embed_formats & MediasiteEmbedFormatValues::PRESENTATION_LINK;
    }
    public function set_lti_embed_type_presentation_link($value) {
        $this->set_lti_embed_type_bitmask($value, MediasiteEmbedFormatValues::PRESENTATION_LINK);
    }
    public function get_lti_embed_type_player_only() {
        return $this->embed_formats & MediasiteEmbedFormatValues::PLAYER_ONLY;
    }
    public function set_lti_embed_type_player_only($value) {
        $this->set_lti_embed_type_bitmask($value, MediasiteEmbedFormatValues::PLAYER_ONLY);
    }

    public function set_lti_embed_type_bitmask($value, $bit) {
        if ($value == 0) {
            $this->embed_formats = $this->embed_formats & ~$bit;
        } else {
            $this->embed_formats = $this->embed_formats | $bit;
        }
    }


    public static function loadbyname($name) {
        global $DB;
        if($record = $DB->get_record('mediasite_sites', array('sitename'=>$name))) {
            $site = new MediasiteSite($record);
            return $site;
        } else {
            return FALSE;
        }
    }
}
