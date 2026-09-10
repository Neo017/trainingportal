<?php
// This file is part of Moodle - https://moodle.org/

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/service/application_service.php');
require_once(__DIR__ . '/classes/service/offering_service.php');

require_login();

$context = context_system::instance();
require_capability('local/trainingportal:viewapplications', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/trainingportal/applications.php'));
$PAGE->set_title(get_string('applications', 'local_trainingportal'));
$PAGE->set_heading(get_string('applications', 'local_trainingportal'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('applications', 'local_trainingportal'));
$filters = ['offeringid' => optional_param('offeringid', 0, PARAM_INT), 'status' => optional_param('status', '', PARAM_ALPHA), 'paymentstatus' => optional_param('paymentstatus', '', PARAM_ALPHA), 'search' => optional_param('search', '', PARAM_TEXT)];
$params = ['offeringid' => $filters['offeringid'], 'status' => $filters['status'], 'paymentstatus' => $filters['paymentstatus'], 'search' => $filters['search']];
echo html_writer::start_tag('form', ['method' => 'get', 'action' => new moodle_url('/local/trainingportal/applications.php')]);
echo html_writer::select(['' => get_string('all'), 'PENDING' => get_string('pending', 'local_trainingportal'), 'APPROVED' => get_string('approved', 'local_trainingportal'), 'REJECTED' => get_string('rejected', 'local_trainingportal')], 'status', $filters['status']);
echo html_writer::select(['' => get_string('all'), 'UPLOADED' => get_string('uploaded', 'local_trainingportal'), 'VERIFIED' => get_string('verified', 'local_trainingportal'), 'INVALID' => get_string('invalid', 'local_trainingportal')], 'paymentstatus', $filters['paymentstatus']);
echo html_writer::empty_tag('input', ['type' => 'text', 'name' => 'search', 'value' => $filters['search'], 'placeholder' => get_string('search')]);
echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('search')]);
echo html_writer::end_tag('form');
$rows = [];
foreach (\local_trainingportal\service\application_service::list($filters) as $application) {
    $rows[] = [s($application->applicationcode), s($application->firstname . ' ' . $application->lastname), s($application->email), s($application->paymentstatus), s($application->status), html_writer::link(new moodle_url('/local/trainingportal/application.php', ['id' => $application->id]), get_string('view'))];
}
$table = new html_table(); $table->head = [get_string('applicationcode', 'local_trainingportal'), get_string('fullname'), get_string('email', 'local_trainingportal'), get_string('paymentstatus', 'local_trainingportal'), get_string('status', 'local_trainingportal'), get_string('actions')]; $table->data = $rows;
echo html_writer::table($table);
echo $OUTPUT->footer();
