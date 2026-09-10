<?php
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/service/application_service.php');
$id = required_param('id', PARAM_INT); $status = required_param('status', PARAM_ALPHA);
require_login(); $context = context_system::instance(); require_capability('local/trainingportal:verifypayment', $context); require_sesskey();
\local_trainingportal\service\application_service::set_payment_status($id, $status);
redirect(new moodle_url('/local/trainingportal/application.php', ['id' => $id]), get_string('changessaved'));
