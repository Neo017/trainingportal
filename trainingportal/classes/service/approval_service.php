<?php
namespace local_trainingportal\service;

defined('MOODLE_INTERNAL') || die();

class approval_service {
    public static function approve(int $id, int $actorid): \stdClass {
        global $DB;
        $application = application_service::get($id);
        if ($application->status === application_service::APPROVED) { return $application; }
        if ($application->status !== application_service::PENDING) { throw new \moodle_exception('invalidstate', 'local_trainingportal'); }
        if ($application->paymentstatus !== application_service::VERIFIED) { throw new \moodle_exception('paymentrequired', 'local_trainingportal'); }
        $offering = offering_service::get($application->offeringid);
        $userid = $application->userid ?: user_service::resolve($application);
        enrolment_service::enrol($userid, (int)$offering->courseid);
        $updated = (object)['id' => $id, 'userid' => $userid, 'approvedby' => $actorid, 'approvedat' => time(), 'status' => application_service::APPROVED, 'timemodified' => time()];
        $DB->update_record('local_tp_application', $updated);
        $application = application_service::get($id);
        \local_trainingportal\event\application_approved::create(['objectid' => $id, 'context' => \context_system::instance(), 'userid' => $actorid])->trigger();
        $user = \core\user::get_user($application->userid, '*', MUST_EXIST);
        notification_service::send($user, get_string('approved', 'local_trainingportal'),
            get_string('approved', 'local_trainingportal') . ': ' . $application->applicationcode . "\n" .
            get_string('courseaccess', 'local_trainingportal') . ': ' .
            (string)new \moodle_url('/course/view.php', ['id' => $offering->courseid]));
        return $application;
    }

    public static function reject(int $id, int $actorid, string $reason): \stdClass {
        global $DB;
        $application = application_service::get($id);
        if ($application->status !== application_service::PENDING) { throw new \moodle_exception('invalidstate', 'local_trainingportal'); }
        $DB->update_record('local_tp_application', (object)['id' => $id, 'status' => application_service::REJECTED, 'rejectedby' => $actorid, 'rejectedat' => time(), 'rejectionreason' => trim($reason), 'timemodified' => time()]);
        \local_trainingportal\event\application_rejected::create(['objectid' => $id, 'context' => \context_system::instance(), 'userid' => $actorid])->trigger();
        $user = (object)['email' => $application->email, 'firstname' => $application->firstname, 'lastname' => $application->lastname, 'maildisplay' => 1, 'mailformat' => FORMAT_PLAIN];
        notification_service::send($user, get_string('rejected', 'local_trainingportal'), trim($reason));
        return application_service::get($id);
    }
}
