# Training Portal

A custom Moodle plugin for managing training registration, payment verification, course approval, enrolment, and certificate issuance.

The plugin acts as a lightweight training-management layer on top of Moodle while reusing Moodle for users, authentication, courses, roles, enrolment, and learning activities.

## Overview

Training Portal provides a structured workflow for managing participants before and after they enter a Moodle course.

```text
Registration
    ↓
Payment Proof Upload
    ↓
Coordinator Review
    ↓
Approve / Reject
    ↓
Moodle User Creation / Linking
    ↓
Course Enrolment
    ↓
Course Access
    ↓
Certificate Issuance
```

## Current Scope

The first release focuses on:

* Training registration
* Course selection
* Fee/payment proof upload
* Application tracking
* Coordinator approval and rejection
* Moodle user creation or existing-user linking
* Automatic course enrolment
* Certificate issuance by coordinator

## Moodle Integration

Training Portal does not modify Moodle core.

It uses Moodle APIs and services for:

* User management
* Authentication
* Courses
* Roles and capabilities
* Enrolment
* File storage
* Notifications
* Activity and course access

Custom functionality is maintained entirely within the plugin.

## Project Structure

```text
trainingportal/
├── classes/
├── db/
├── lang/
├── templates/
├── amd/
│   └── src/
├── pix/
├── tests/
├── register.php
├── applications.php
├── version.php
├── README.md
├── INSTALL.md
└── .gitignore
```

## Plugin Location

For Moodle 5.x:

```text
public/local/trainingportal/
```

## Development Principles

* Do not modify Moodle core.
* Use Moodle APIs instead of direct database manipulation.
* Keep workflow logic inside the plugin.
* Use Moodle roles and capabilities for access control.
* Use Moodle File API for uploaded documents.
* Keep registration data separate from Moodle user creation until approval.

## Workflow States

Applications currently follow:

```text
PENDING
APPROVED
REJECTED
```

A Moodle account and course enrolment are created or linked only after approval.

## Planned Extensions

Future versions may include:

* Attendance tracking
* Moodle grade integration
* Project verification
* Certificate eligibility rules
* Participant progress dashboard
* QR certificate verification
* Training reports and analytics

## Repository

```text
git@github.com:Neo017/trainingportal.git
```

## Status

## Implemented

The current Sprint 1 implementation includes the Moodle plugin bootstrap,
offering and application tables, capabilities, public registration, payment
proof storage, coordinator payment review, approval/rejection orchestration,
Moodle user linking/creation, manual course enrolment, event logging,
certificate PDF storage, privacy metadata, and basic PHPUnit coverage.

Install the plugin in a Moodle 5.2 site, configure a manual enrolment method on
the target course, and assign the Training Portal capabilities to coordinators.
