<?php
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/service/approval_service.php');
$id = required_param('id', PARAM_INT); require_login(); $context = context_system::instance(); require_capability('local/trainingportal:approveapplications', $context); require_sesskey();
try { \local_trainingportal\service\approval_service::approve($id, $USER->id); redirect(new moodle_url('/local/trainingportal/application.php', ['id' => $id]), get_string('approved', 'local_trainingportal')); }
catch (\Throwable $exception) { print_error('error'); }
