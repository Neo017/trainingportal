<?php
namespace local_trainingportal\event;
defined('MOODLE_INTERNAL') || die();
class certificate_issued extends \core\event\base {
    protected function init() { $this->data['objecttable'] = 'local_tp_certificate'; $this->data['crud'] = 'c'; $this->data['edulevel'] = self::LEVEL_OTHER; }
    public static function get_name() { return get_string('certificateissued', 'local_trainingportal'); }
    public function get_description() { return 'Training certificate ' . $this->objectid . ' was issued.'; }
}
