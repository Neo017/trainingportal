## Installation

Copy this directory to the Moodle installation at:

```text
public/local/trainingportal/
```

Log in as a Moodle administrator and complete the upgrade prompt. Moodle
will install the plugin as `local_trainingportal`.

The current version implements the Sprint 1 workflow. After installation:

1. Enable manual enrolment on each Moodle course used by an offering.
2. Create or configure a Training Coordinator role with the capabilities
   declared in `db/access.php`.
3. Open `/local/trainingportal/offerings.php` and create an active offering.
4. Open `/local/trainingportal/register.php` in a private browser window and
   submit a test application with a PDF, JPEG, or PNG payment proof.
5. As a coordinator, verify payment, approve the application, and confirm the
   user appears in the Moodle course participants list.
6. Issue a certificate from the approved application and verify its download.

The plugin requires Moodle 5.2 or later. PHP/Moodle runtime tests must be run
on a Moodle development site; this repository does not include Moodle core.
