<?php
namespace local_trainingportal;
defined('MOODLE_INTERNAL') || die();

class approval_service_test extends \advanced_testcase {
    public function test_approval_service_is_namespaced(): void {
        $this->assertTrue(class_exists('local_trainingportal\\service\\approval_service'));
    }
}
