<?php
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/form/rejection_form.php');
require_once(__DIR__ . '/classes/service/approval_service.php');
$id = required_param('id', PARAM_INT); require_login(); $context = context_system::instance(); require_capability('local/trainingportal:rejectapplications', $context);
$PAGE->set_context($context); $PAGE->set_url(new moodle_url('/local/trainingportal/reject.php', ['id' => $id])); $PAGE->set_title(get_string('reject', 'local_trainingportal')); $PAGE->set_heading(get_string('reject', 'local_trainingportal'));
$form = new \local_trainingportal\form\rejection_form();
if ($form->is_cancelled()) { redirect(new moodle_url('/local/trainingportal/application.php', ['id' => $id])); }
if ($data = $form->get_data()) { \local_trainingportal\service\approval_service::reject($id, $USER->id, $data->rejectionreason); redirect(new moodle_url('/local/trainingportal/application.php', ['id' => $id]), get_string('rejected', 'local_trainingportal')); }
echo $OUTPUT->header(); $form->display(); echo $OUTPUT->footer();
