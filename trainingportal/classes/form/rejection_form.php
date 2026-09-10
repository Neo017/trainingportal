<?php
namespace local_trainingportal\form;

defined('MOODLE_INTERNAL') || die();

class rejection_form extends \moodleform {
    protected function definition() {
        $mform = $this->_form;
        $mform->addElement('textarea', 'rejectionreason', get_string('rejectionreason', 'local_trainingportal'), ['rows' => 5, 'cols' => 60]);
        $mform->setType('rejectionreason', PARAM_TEXT);
        $mform->addRule('rejectionreason', null, 'required');
        $this->add_action_buttons(true, get_string('reject', 'local_trainingportal'));
    }
}
