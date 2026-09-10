<?php
// This file is part of Moodle - https://moodle.org/

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/form/registration_form.php');
require_once(__DIR__ . '/classes/service/offering_service.php');
require_once(__DIR__ . '/classes/service/application_service.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/trainingportal/register.php'));
$PAGE->set_title(get_string('registration', 'local_trainingportal'));
$PAGE->set_heading(get_string('registration', 'local_trainingportal'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('registration', 'local_trainingportal'));
$offerings = \local_trainingportal\service\offering_service::all(true);
$offerings = array_filter($offerings, function($offering) { return time() >= $offering->registrationstart && time() <= $offering->registrationend; });
if (!$offerings) {
    echo $OUTPUT->notification(get_string('registrationclosed', 'local_trainingportal'), 'info');
} else {
    $form = new \local_trainingportal\form\registration_form(null, ['offerings' => $offerings]);
    if ($form->is_cancelled()) { redirect(new moodle_url('/local/trainingportal/index.php')); }
    if ($data = $form->get_data()) {
        try {
            $application = \local_trainingportal\service\application_service::create($data, (int)$data->paymentproof);
            echo $OUTPUT->notification(get_string('applicationcreated', 'local_trainingportal', $application->applicationcode), 'success');
        } catch (\Throwable $exception) {
            echo $OUTPUT->notification(get_string('error'), 'error');
        }
    } else {
        $form->display();
    }
}
echo $OUTPUT->footer();
