<?php
namespace local_trainingportal\event;
defined('MOODLE_INTERNAL') || die();
class application_created extends \core\event\base {
    protected function init() { $this->data['objecttable'] = 'local_tp_application'; $this->data['crud'] = 'c'; $this->data['edulevel'] = self::LEVEL_OTHER; }
    public static function get_name() { return get_string('applicationcreated', 'local_trainingportal'); }
    public function get_description() { return 'Training application ' . $this->objectid . ' was created.'; }
}
