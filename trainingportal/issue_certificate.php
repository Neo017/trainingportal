<?php
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/service/certificate_service.php');
$id = required_param('id', PARAM_INT); require_login(); $context = context_system::instance(); require_capability('local/trainingportal:issuecertificates', $context); require_sesskey();
$certificate = \local_trainingportal\service\certificate_service::issue($id, $USER->id);
redirect(new moodle_url('/local/trainingportal/certificate.php', ['id' => $certificate->id]), get_string('certificateissued', 'local_trainingportal'));
