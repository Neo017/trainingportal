<?php
namespace local_trainingportal\privacy;
defined('MOODLE_INTERNAL') || die();

class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {
    public static function get_metadata(\core_privacy\local\metadata\collection $collection): \core_privacy\local\metadata\collection {
        $collection->add_database_table('local_tp_application', [
            'firstname' => 'privacy:metadata:application:firstname', 'lastname' => 'privacy:metadata:application:lastname',
            'email' => 'privacy:metadata:application:email', 'phone' => 'privacy:metadata:application:phone',
            'organisation' => 'privacy:metadata:application:organisation', 'designation' => 'privacy:metadata:application:designation',
            'paymentreference' => 'privacy:metadata:application:paymentreference', 'userid' => 'privacy:metadata:application:userid',
        ], 'privacy:metadata:application');
        $collection->add_database_table('local_tp_certificate', ['userid' => 'privacy:metadata:certificate:userid'], 'privacy:metadata:certificate');
        $collection->add_subsystem_link('core_files', [], 'privacy:metadata:files');
        return $collection;
    }
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
        global $DB;
        if ($userlist->get_context()->contextlevel != CONTEXT_SYSTEM) { return; }
        foreach ($DB->get_records_select('local_tp_application', 'userid IS NOT NULL', [], '', 'userid') as $record) { $userlist->add_user($record->userid); }
        foreach ($DB->get_records('local_tp_certificate', [], '', 'userid') as $record) { $userlist->add_user($record->userid); }
    }
    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist) {
        global $DB;
        $context = \context_system::instance();
        foreach ($contextlist->get_userids() as $userid) {
            $data = $DB->get_records('local_tp_application', ['userid' => $userid]);
            \core_privacy\local\request\writer::with_context($context)->export_data(['applications'], (object)['applications' => array_values($data)]);
        }
    }
    public static function delete_data_for_all_users_in_context(\context $context) { if ($context->contextlevel == CONTEXT_SYSTEM) { self::delete_all($context); } }
    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist) {
        global $DB;
        foreach ($contextlist->get_userids() as $userid) {
            $applications = $DB->get_records('local_tp_application', ['userid' => $userid], '', 'id');
            $certificates = $DB->get_records('local_tp_certificate', ['userid' => $userid], '', 'id');
            self::delete_files($applications, 'paymentproof');
            self::delete_files($certificates, 'certificate');
            $DB->delete_records('local_tp_application', ['userid' => $userid]);
            $DB->delete_records('local_tp_certificate', ['userid' => $userid]);
        }
    }
    private static function delete_all(\context $context) {
        global $DB;
        self::delete_files($DB->get_records('local_tp_application', [], '', 'id'), 'paymentproof');
        self::delete_files($DB->get_records('local_tp_certificate', [], '', 'id'), 'certificate');
        $DB->delete_records('local_tp_application');
        $DB->delete_records('local_tp_certificate');
    }

    private static function delete_files(array $records, string $filearea): void {
        $fs = get_file_storage();
        foreach ($records as $record) {
            $fs->delete_area_files(\context_system::instance()->id, 'local_trainingportal', $filearea, $record->id);
        }
    }
}
