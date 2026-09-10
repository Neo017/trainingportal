<?php
namespace local_trainingportal\service;

defined('MOODLE_INTERNAL') || die();

class enrolment_service {
    public static function is_enrolled(int $userid, int $courseid): bool {
        $context = \context_course::instance($courseid);
        return is_enrolled($context, $userid, '', true);
    }

    public static function enrol(int $userid, int $courseid): void {
        $course = get_course($courseid);
        $instances = enrol_get_instances($course->id, true);
        $manual = null;
        foreach ($instances as $instance) { if ($instance->enrol === 'manual') { $manual = $instance; break; } }
        if (!$manual) { throw new \moodle_exception('enrolmentfailed', 'local_trainingportal'); }
        $plugin = enrol_get_plugin('manual');
        $student = get_archetype_roles('student');
        $roleid = $student ? reset($student)->id : 0;
        if (!$roleid) { throw new \moodle_exception('enrolmentfailed', 'local_trainingportal'); }
        if (!self::is_enrolled($userid, $course->id)) {
            $plugin->enrol_user($manual, $userid, $roleid, time());
        }
    }
}
