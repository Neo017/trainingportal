<?php
require(__DIR__ . '/../../config.php');
$id = required_param('id', PARAM_INT); require_login(); $context = context_system::instance();
$certificate = $DB->get_record('local_tp_certificate', ['id' => $id], '*', MUST_EXIST);
if ($certificate->userid != $USER->id) { require_capability('local/trainingportal:issuecertificates', $context); }
$fs = get_file_storage(); $files = $fs->get_area_files($context->id, 'local_trainingportal', 'certificate', $id, 'filename', false); $file = reset($files);
if (!$file) { print_error('filenotfound'); }
send_stored_file($file, 0, 0, true, ['filename' => $file->get_filename(), 'dontdie' => false]);
