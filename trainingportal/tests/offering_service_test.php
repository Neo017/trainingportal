<?php
namespace local_trainingportal;
defined('MOODLE_INTERNAL') || die();

class offering_service_test extends \advanced_testcase {
    public function test_closed_offering_is_rejected(): void {
        global $DB;
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $id = $DB->insert_record('local_tp_offering', (object)[
            'courseid' => $course->id, 'code' => 'CLOSED', 'title' => 'Closed',
            'registrationstart' => time() - 3600, 'registrationend' => time() - 60,
            'trainingstart' => time(), 'trainingend' => time() + 3600, 'fee' => 0,
            'capacity' => 10, 'active' => 1, 'timecreated' => time(), 'timemodified' => time(),
        ]);
        $this->expectException(\moodle_exception::class);
        \local_trainingportal\service\offering_service::accepting($id);
    }

    public function test_capacity_is_enforced(): void {
        global $DB;
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $now = time();
        $offeringid = $DB->insert_record('local_tp_offering', (object)[
            'courseid' => $course->id, 'code' => 'FULL', 'title' => 'Full',
            'registrationstart' => $now - 3600, 'registrationend' => $now + 3600,
            'trainingstart' => $now, 'trainingend' => $now + 3600, 'fee' => 0,
            'capacity' => 1, 'active' => 1, 'timecreated' => $now, 'timemodified' => $now,
        ]);
        $DB->insert_record('local_tp_application', (object)[
            'offeringid' => $offeringid, 'applicationcode' => 'TRN-TEST-000001',
            'firstname' => 'A', 'lastname' => 'B', 'email' => 'a@example.com', 'phone' => '1',
            'paymentreference' => 'P', 'paymentstatus' => 'VERIFIED', 'status' => 'PENDING',
            'timecreated' => $now, 'timemodified' => $now,
        ]);
        $this->expectException(\moodle_exception::class);
        \local_trainingportal\service\offering_service::accepting($offeringid);
    }
}
