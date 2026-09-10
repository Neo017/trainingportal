<?php
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/service/application_service.php');
require_once(__DIR__ . '/classes/service/offering_service.php');
$id = required_param('id', PARAM_INT); require_login(); $context = context_system::instance(); require_capability('local/trainingportal:viewapplications', $context);
$application = \local_trainingportal\service\application_service::get($id); $offering = \local_trainingportal\service\offering_service::get($application->offeringid);
$PAGE->set_context($context); $PAGE->set_url(new moodle_url('/local/trainingportal/application.php', ['id' => $id])); $PAGE->set_title($application->applicationcode); $PAGE->set_heading($application->applicationcode);
echo $OUTPUT->header();
foreach (['applicationcode', 'firstname', 'lastname', 'email', 'phone', 'organisation', 'designation', 'paymentreference', 'paymentstatus', 'status'] as $field) { echo html_writer::tag('p', html_writer::tag('strong', get_string($field, 'local_trainingportal') . ': ') . s($application->$field)); }
echo html_writer::tag('p', html_writer::tag('strong', get_string('offering', 'local_trainingportal') . ': ') . s($offering->title));
if (has_capability('local/trainingportal:viewpaymentproof', $context)) {
    $files = get_file_storage()->get_area_files($context->id, 'local_trainingportal', 'paymentproof', $id, 'filename', false);
    $proof = reset($files);
    if ($proof) {
        $url = moodle_url::make_pluginfile_url($context->id, 'local_trainingportal', 'paymentproof', $id, $proof->get_filepath(), $proof->get_filename(), true);
        echo html_writer::link($url, get_string('paymentproof', 'local_trainingportal'));
    }
}
if ($application->status === 'PENDING') {
    if ($application->paymentstatus === 'UPLOADED' && has_capability('local/trainingportal:verifypayment', $context)) { echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/payment.php', ['id' => $id, 'status' => 'VERIFIED']), get_string('verify', 'local_trainingportal')); echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/payment.php', ['id' => $id, 'status' => 'INVALID']), get_string('markinvalid', 'local_trainingportal')); }
    if ($application->paymentstatus === 'VERIFIED' && has_capability('local/trainingportal:approveapplications', $context)) { echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/approve.php', ['id' => $id]), get_string('approve', 'local_trainingportal')); }
    if (has_capability('local/trainingportal:rejectapplications', $context)) { echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/reject.php', ['id' => $id]), get_string('reject', 'local_trainingportal')); }
}
if ($application->status === 'APPROVED' && has_capability('local/trainingportal:issuecertificates', $context)) {
    $certificate = $DB->get_record('local_tp_certificate', ['applicationid' => $id]);
    if ($certificate) { echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/certificate.php', ['id' => $certificate->id]), get_string('downloadcertificate', 'local_trainingportal')); }
    else { echo $OUTPUT->single_button(new moodle_url('/local/trainingportal/issue_certificate.php', ['id' => $id]), get_string('issuecertificate', 'local_trainingportal')); }
}
echo $OUTPUT->footer();
