<?php
namespace local_trainingportal\form;

defined('MOODLE_INTERNAL') || die();

class offering_form extends \moodleform {
    protected function definition() {
        global $DB;
        $mform = $this->_form;
        $courses = $DB->get_records_menu('course', ['visible' => 1], 'fullname ASC', 'id,fullname');
        $mform->addElement('select', 'courseid', get_string('course', 'local_trainingportal'), $courses);
        foreach (['code', 'title'] as $name) { $mform->addElement('text', $name, get_string($name, 'local_trainingportal')); $mform->setType($name, PARAM_TEXT); $mform->addRule($name, null, 'required'); }
        foreach (['registrationstart', 'registrationend', 'trainingstart', 'trainingend'] as $name) { $mform->addElement('date_time_selector', $name, get_string($name, 'local_trainingportal')); }
        $mform->addElement('text', 'fee', get_string('fee', 'local_trainingportal')); $mform->setType('fee', PARAM_LOCALISEDFLOAT);
        $mform->addElement('text', 'capacity', get_string('capacity', 'local_trainingportal')); $mform->setType('capacity', PARAM_INT);
        $mform->addElement('advcheckbox', 'active', get_string('active', 'local_trainingportal'));
        $this->add_action_buttons();
    }
}
