<?php
namespace local_trainingportal\service;

defined('MOODLE_INTERNAL') || die();

class certificate_service {
    public static function issue(int $applicationid, int $actorid): \stdClass {
        global $DB;
        $application = application_service::get($applicationid);
        if ($application->status !== application_service::APPROVED || !$application->userid) { throw new \moodle_exception('invalidstate', 'local_trainingportal'); }
        $existing = $DB->get_record('local_tp_certificate', ['applicationid' => $applicationid]);
        if ($existing) { return $existing; }
        $offering = offering_service::get($application->offeringid);
        if (!enrolment_service::is_enrolled((int)$application->userid, (int)$offering->courseid)) { throw new \moodle_exception('enrolmentfailed', 'local_trainingportal'); }
        $number = sprintf('ICFOSS-%s-%s-%04d', strtoupper($offering->code), date('Y'), $applicationid);
        $provider = new \local_trainingportal\local\certificate\simple_pdf_provider();
        $pdf = $provider->generate($application, $offering, $number);
        $now = time();
        $record = (object)['applicationid' => $applicationid, 'offeringid' => $offering->id, 'courseid' => $offering->courseid, 'userid' => $application->userid, 'certificatenumber' => $number, 'issuedby' => $actorid, 'timeissued' => $now, 'status' => 'ISSUED', 'timecreated' => $now, 'timemodified' => $now];
        $record->id = $DB->insert_record('local_tp_certificate', $record);
        $fs = get_file_storage();
        $fs->create_file_from_string(['contextid' => \context_system::instance()->id, 'component' => 'local_trainingportal', 'filearea' => 'certificate', 'itemid' => $record->id, 'filepath' => '/', 'filename' => $number . '.pdf'], $pdf);
        \local_trainingportal\event\certificate_issued::create(['objectid' => $record->id, 'context' => \context_system::instance(), 'userid' => $actorid])->trigger();
        $user = \core\user::get_user($application->userid, '*', MUST_EXIST);
        notification_service::send($user, get_string('certificateissued', 'local_trainingportal'),
            $number . "\n" . (string)new \moodle_url('/local/trainingportal/certificate.php', ['id' => $record->id]));
        return $record;
    }
}
