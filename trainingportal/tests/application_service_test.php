<?php
namespace local_trainingportal;
defined('MOODLE_INTERNAL') || die();

class application_service_test extends \advanced_testcase {
    public function test_application_states_are_independent(): void {
        $this->assertSame('PENDING', \local_trainingportal\service\application_service::PENDING);
        $this->assertSame('VERIFIED', \local_trainingportal\service\application_service::VERIFIED);
        $this->assertNotSame(\local_trainingportal\service\application_service::PENDING, \local_trainingportal\service\application_service::VERIFIED);
    }

    public function test_valid_application_is_created_and_duplicate_is_rejected(): void {
        global $DB, $USER;
        $this->resetAfterTest(true);
        set_config('noemailever', true);
        $this->setUser($this->getDataGenerator()->create_user());
        $draftitemid = file_get_unused_draft_itemid();
        get_file_storage()->create_file_from_string([
            'contextid' => \context_user::instance($USER->id)->id, 'component' => 'user',
            'filearea' => 'draft', 'itemid' => $draftitemid, 'filepath' => '/',
            'filename' => 'payment.pdf',
        ], '%PDF-test');
        $course = $this->getDataGenerator()->create_course();
        $now = time();
        $offeringid = $DB->insert_record('local_tp_offering', (object)[
            'courseid' => $course->id, 'code' => 'TEST-OPEN', 'title' => 'Open test',
            'registrationstart' => $now - 60, 'registrationend' => $now + 3600,
            'trainingstart' => $now + 3600, 'trainingend' => $now + 7200, 'fee' => 0,
            'capacity' => 10, 'active' => 1, 'timecreated' => $now, 'timemodified' => $now,
        ]);
        $data = (object)['offeringid' => $offeringid, 'firstname' => 'Test', 'lastname' => 'Applicant',
            'email' => 'test@example.com', 'phone' => '1234567890', 'organisation' => '',
            'designation' => '', 'paymentreference' => 'PAY-1'];
        $application = \local_trainingportal\service\application_service::create($data, $draftitemid);
        $this->assertSame('PENDING', $application->status);
        $this->assertStringStartsWith('TRN-', $application->applicationcode);
        $this->expectException(\moodle_exception::class);
        \local_trainingportal\service\application_service::create($data, $draftitemid);
    }
}
