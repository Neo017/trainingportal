<?php
// This file is part of Moodle - https://moodle.org/

require(__DIR__ . '/../../config.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/trainingportal/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_trainingportal'));
$PAGE->set_heading(get_string('pluginname', 'local_trainingportal'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_trainingportal'));
echo $OUTPUT->box(get_string('pluginintro', 'local_trainingportal'));
echo $OUTPUT->single_button(
    new moodle_url('/local/trainingportal/register.php'),
    get_string('registration', 'local_trainingportal')
);
if (has_capability('local/trainingportal:manageofferings', $context)) {
    echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/offerings.php'), get_string('offerings', 'local_trainingportal'));
}
if (has_capability('local/trainingportal:viewapplications', $context)) {
    echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/applications.php'), get_string('applications', 'local_trainingportal'));
}
echo $OUTPUT->footer();
