<?php
namespace local_trainingportal\service;

defined('MOODLE_INTERNAL') || die();

class user_service {
    public static function resolve(\stdClass $application): int {
        global $DB, $CFG;
        $email = strtolower(trim($application->email));
        $user = $DB->get_record('user', ['email' => $email, 'deleted' => 0], 'id', IGNORE_MULTIPLE);
        if ($user) { return (int)$user->id; }
        $user = (object)[
            'auth' => 'manual', 'confirmed' => 1, 'mnethostid' => $CFG->mnet_localhost_id,
            'username' => self::username($email), 'password' => \AUTH_PASSWORD_NOT_CACHED,
            'firstname' => $application->firstname, 'lastname' => $application->lastname,
            'email' => $email, 'phone1' => $application->phone,
            'timecreated' => time(), 'timemodified' => time(),
        ];
        return (int)\core\user::create_user($user, false, false);
    }

    private static function username(string $email): string {
        global $DB, $CFG;
        [$base] = explode('@', $email, 2);
        $base = preg_replace('/[^a-z0-9._-]/', '', strtolower($base)) ?: 'participant';
        $username = $base;
        $suffix = 1;
        while ($DB->record_exists('user', ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id])) { $username = $base . $suffix++; }
        return $username;
    }
}
