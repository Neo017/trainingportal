<?php
namespace local_trainingportal\local\certificate;
defined('MOODLE_INTERNAL') || die();

interface provider_interface {
    public function generate(\stdClass $application, \stdClass $offering, string $certificatenumber): string;
}
