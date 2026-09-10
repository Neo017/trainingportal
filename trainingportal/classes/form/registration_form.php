<?php
namespace local_trainingportal\form;

defined('MOODLE_INTERNAL') || die();

class registration_form extends \moodleform {
    protected function definition() {
        $mform = $this->_form;
        $offerings = $this->_customdata['offerings'];
        $options = [];
        foreach ($offerings as $offering) { $options[$offering->id] = format_string($offering->title) . ' (' . s($offering->code) . ')'; }
        $mform->addElement('select', 'offeringid', get_string('offering', 'local_trainingportal'), $options);
        $mform->addRule('offeringid', null, 'required');
        foreach (['firstname', 'lastname', 'email', 'phone', 'organisation', 'designation', 'paymentreference'] as $name) {
            $mform->addElement('text', $name, get_string($name, 'local_trainingportal'));
            $mform->setType($name, $name === 'email' ? PARAM_EMAIL : PARAM_NOTAGS);
            if (in_array($name, ['firstname', 'lastname', 'email', 'phone', 'paymentreference'], true)) { $mform->addRule($name, null, 'required'); }
        }
        $mform->addElement('filepicker', 'paymentproof', get_string('paymentproof', 'local_trainingportal'), null, ['maxbytes' => 10485760, 'accepted_types' => ['.pdf', '.jpg', '.jpeg', '.png']]);
        $mform->addRule('paymentproof', null, 'required');
        $mform->addElement('checkbox', 'consent', get_string('consent', 'local_trainingportal'));
        $mform->addRule('consent', null, 'required');
        $this->add_action_buttons(true, get_string('submitapplication', 'local_trainingportal'));
    }

    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if (!empty($data['email']) && !validate_email($data['email'])) { $errors['email'] = get_string('invalidemail'); }
        return $errors;
    }
}
