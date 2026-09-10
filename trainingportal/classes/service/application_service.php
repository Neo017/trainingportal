<?php
namespace local_trainingportal\service;

defined('MOODLE_INTERNAL') || die();

class application_service {
    public const PENDING = 'PENDING';
    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';
    public const UPLOADED = 'UPLOADED';
    public const VERIFIED = 'VERIFIED';
    public const INVALID = 'INVALID';

    public static function get(int $id): \stdClass {
        global $DB;
        return $DB->get_record('local_tp_application', ['id' => $id], '*', MUST_EXIST);
    }

    public static function find_by_code(string $code): \stdClass {
        global $DB;
        return $DB->get_record('local_tp_application', ['applicationcode' => $code], '*', MUST_EXIST);
    }

    public static function list(array $filters = []): array {
        global $DB;
        $where = [];
        $params = [];
        if (!empty($filters['offeringid'])) { $where[] = 'offeringid = :offeringid'; $params['offeringid'] = (int)$filters['offeringid']; }
        if (!empty($filters['status'])) { $where[] = 'status = :status'; $params['status'] = $filters['status']; }
        if (!empty($filters['paymentstatus'])) { $where[] = 'paymentstatus = :paymentstatus'; $params['paymentstatus'] = $filters['paymentstatus']; }
        if (!empty($filters['search'])) {
            $where[] = $DB->sql_like($DB->sql_concat('firstname', "' '", 'lastname'), ':search', false);
            $params['search'] = '%' . $DB->sql_like_escape(trim($filters['search'])) . '%';
        }
        return $DB->get_records_select('local_tp_application', $where ? implode(' AND ', $where) : '1=1', $params, 'timecreated DESC');
    }

    public static function create(\stdClass $data, int $draftitemid): \stdClass {
        global $DB, $USER;
        foreach (['firstname', 'lastname', 'email', 'phone', 'paymentreference'] as $field) {
            if (empty($data->$field) || !is_string($data->$field) || trim($data->$field) === '') {
                throw new \moodle_exception('required');
            }
        }
        if (!validate_email(trim($data->email))) { throw new \moodle_exception('invalidemail'); }
        if (!preg_match('/^[0-9 +().-]{7,50}$/', trim($data->phone))) { throw new \moodle_exception('invalidparameter'); }
        $offering = offering_service::accepting((int)$data->offeringid);
        $email = strtolower(trim($data->email));
        $existing = $DB->get_record_select('local_tp_application',
            'offeringid = :offeringid AND email = :email AND status <> :rejected',
            ['offeringid' => $offering->id, 'email' => $email, 'rejected' => self::REJECTED]);
        if ($existing) {
            throw new \moodle_exception('duplicateapplication', 'local_trainingportal', '', $existing->applicationcode);
        }
        $record = (object)[
            'offeringid' => $offering->id, 'applicationcode' => '',
            'firstname' => trim($data->firstname), 'lastname' => trim($data->lastname),
            'email' => $email, 'phone' => trim($data->phone),
            'organisation' => trim($data->organisation ?? ''), 'designation' => trim($data->designation ?? ''),
            'paymentreference' => trim($data->paymentreference), 'paymentstatus' => self::UPLOADED,
            'status' => self::PENDING, 'timecreated' => time(), 'timemodified' => time(),
        ];
        $transaction = $DB->start_delegated_transaction();
        $record->id = $DB->insert_record('local_tp_application', $record);
        $record->applicationcode = sprintf('TRN-%s-%06d', date('Y'), $record->id);
        $DB->set_field('local_tp_application', 'applicationcode', $record->applicationcode, ['id' => $record->id]);
        $fs = get_file_storage();
        file_save_draft_area_files($draftitemid, \context_system::instance()->id, 'local_trainingportal', 'paymentproof',
            $record->id, ['subdirs' => 0, 'maxfiles' => 1, 'maxbytes' => 10485760,
                'accepted_types' => ['.pdf', '.jpg', '.jpeg', '.png']]);
        if (!$fs->get_area_files(\context_system::instance()->id, 'local_trainingportal', 'paymentproof', $record->id, 'filename', false)) {
            throw new \moodle_exception('invalidparameter');
        }
        $transaction->allow_commit();
        $event = \local_trainingportal\event\application_created::create(['objectid' => $record->id, 'context' => \context_system::instance(), 'other' => ['applicationcode' => $record->applicationcode]]);
        $event->trigger();
        notification_service::send_to_email($record->email, $record->firstname, $record->lastname,
            get_string('applicationsubmitted', 'local_trainingportal'),
            get_string('applicationcreated', 'local_trainingportal', $record->applicationcode));
        return $record;
    }

    public static function set_payment_status(int $id, string $status): void {
        global $DB;
        if (!in_array($status, [self::VERIFIED, self::INVALID], true)) { throw new \coding_exception('Invalid payment status'); }
        $application = self::get($id);
        if ($application->status !== self::PENDING || $application->paymentstatus !== self::UPLOADED) { throw new \moodle_exception('invalidstate', 'local_trainingportal'); }
        $DB->update_record('local_tp_application', (object)['id' => $id, 'paymentstatus' => $status, 'timemodified' => time()]);
    }
}
