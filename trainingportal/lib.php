<?php
defined('MOODLE_INTERNAL') || die();

function local_trainingportal_extend_navigation(global_navigation $navigation) {
    if (has_capability('local/trainingportal:viewapplications', context_system::instance())) {
        $navigation->add(get_string('pluginname', 'local_trainingportal'), new moodle_url('/local/trainingportal/index.php'));
    }
}

function local_trainingportal_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $DB;
    if ($context->contextlevel != CONTEXT_SYSTEM || !in_array($filearea, ['paymentproof', 'certificate'], true)) { send_file_not_found(); }
    require_login();
    if ($filearea === 'paymentproof') {
        require_capability('local/trainingportal:viewpaymentproof', $context);
    } else {
        $certificate = $DB->get_record('local_tp_certificate', ['id' => (int)$args[0]], '*', MUST_EXIST);
        if ($certificate->userid != $GLOBALS['USER']->id) { require_capability('local/trainingportal:issuecertificates', $context); }
    }
    $itemid = (int)array_shift($args);
    $filename = array_pop($args);
    $filepath = '/' . ($args ? implode('/', $args) . '/' : '');
    $file = get_file_storage()->get_file($context->id, 'local_trainingportal', $filearea, $itemid, $filepath, $filename);
    if (!$file || $file->is_directory()) { send_file_not_found(); }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}
