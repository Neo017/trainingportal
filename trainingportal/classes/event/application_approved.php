<?php
namespace local_trainingportal\event;
defined('MOODLE_INTERNAL') || die();
class application_approved extends \core\event\base {
    protected function init() { $this->data['objecttable'] = 'local_tp_application'; $this->data['crud'] = 'u'; $this->data['edulevel'] = self::LEVEL_OTHER; }
    public static function get_name() { return get_string('approve', 'local_trainingportal'); }
    public function get_description() { return 'Training application ' . $this->objectid . ' was approved.'; }
}
