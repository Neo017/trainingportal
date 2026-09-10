<?php
namespace local_trainingportal\service;

defined('MOODLE_INTERNAL') || die();

class notification_service {
    public static function send(\stdClass $user, string $subject, string $body): bool {
        return email_to_user($user, get_admin(), $subject, $body);
    }

    public static function send_to_email(string $email, string $firstname, string $lastname, string $subject, string $body): bool {
        return self::send((object)[
            'id' => 0, 'email' => $email, 'firstname' => $firstname, 'lastname' => $lastname,
            'maildisplay' => 1, 'mailformat' => FORMAT_PLAIN,
        ], $subject, $body);
    }
}
