<?php
namespace local_trainingportal\service;

defined('MOODLE_INTERNAL') || die();

class offering_service {
    public static function get(int $id): \stdClass {
        global $DB;
        return $DB->get_record('local_tp_offering', ['id' => $id], '*', MUST_EXIST);
    }

    public static function all(bool $activeonly = false): array {
        global $DB;
        return $DB->get_records('local_tp_offering', $activeonly ? ['active' => 1] : [], 'trainingstart ASC, id ASC');
    }

    public static function save(\stdClass $data, ?int $id = null): int {
        global $DB;
        if (!get_course($data->courseid)) {
            throw new \moodle_exception('invalidcourseid');
        }
        $data->code = strtoupper(trim($data->code));
        $data->timemodified = time();
        if ($id) {
            $data->id = $id;
            $DB->update_record('local_tp_offering', $data);
            return $id;
        }
        $data->timecreated = time();
        return $DB->insert_record('local_tp_offering', $data);
    }

    public static function accepting(int $id, ?int $now = null): \stdClass {
        $offering = self::get($id);
        $now = $now ?? time();
        if (!$offering->active || $now < $offering->registrationstart || $now > $offering->registrationend) {
            throw new \moodle_exception('registrationclosed', 'local_trainingportal');
        }
        if ($offering->capacity > 0) {
            global $DB;
            $occupied = $DB->count_records_select('local_tp_application',
                'offeringid = :offeringid AND status IN (:pending, :approved)',
                ['offeringid' => $offering->id, 'pending' => application_service::PENDING,
                    'approved' => application_service::APPROVED]);
            if ($occupied >= $offering->capacity) {
                throw new \moodle_exception('capacityfull', 'local_trainingportal');
            }
        }
        return $offering;
    }
}
