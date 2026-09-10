<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/trainingportal:manageofferings' => ['riskbitmask' => RISK_DATALOSS, 'captype' => 'write', 'contextlevel' => CONTEXT_SYSTEM, 'archetypes' => ['manager' => CAP_ALLOW]],
    'local/trainingportal:viewapplications' => ['riskbitmask' => RISK_PERSONAL, 'captype' => 'read', 'contextlevel' => CONTEXT_SYSTEM, 'archetypes' => ['manager' => CAP_ALLOW]],
    'local/trainingportal:viewpaymentproof' => ['riskbitmask' => RISK_PERSONAL, 'captype' => 'read', 'contextlevel' => CONTEXT_SYSTEM, 'archetypes' => ['manager' => CAP_ALLOW]],
    'local/trainingportal:verifypayment' => ['riskbitmask' => RISK_PERSONAL, 'captype' => 'write', 'contextlevel' => CONTEXT_SYSTEM, 'archetypes' => ['manager' => CAP_ALLOW]],
    'local/trainingportal:approveapplications' => ['riskbitmask' => RISK_PERSONAL, 'captype' => 'write', 'contextlevel' => CONTEXT_SYSTEM, 'archetypes' => ['manager' => CAP_ALLOW]],
    'local/trainingportal:rejectapplications' => ['riskbitmask' => RISK_PERSONAL, 'captype' => 'write', 'contextlevel' => CONTEXT_SYSTEM, 'archetypes' => ['manager' => CAP_ALLOW]],
    'local/trainingportal:issuecertificates' => ['riskbitmask' => RISK_PERSONAL, 'captype' => 'write', 'contextlevel' => CONTEXT_SYSTEM, 'archetypes' => ['manager' => CAP_ALLOW]],
];
