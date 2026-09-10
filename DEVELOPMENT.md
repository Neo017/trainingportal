# Training Portal — Development Specification

## 1. Project Overview

**Training Portal** is a custom Moodle local plugin that adds a training-administration workflow on top of Moodle.

The plugin is responsible for the lifecycle that exists around an LMS course:

```text
Training Offering
      ↓
Public Registration
      ↓
Payment Proof Upload
      ↓
Application Review
      ↓
Approval / Rejection
      ↓
Moodle User Creation or Linking
      ↓
Course Enrolment
      ↓
Course Participation
      ↓
Certificate Issuance
```

Moodle remains responsible for the LMS functionality itself:

- Authentication
- User accounts
- Courses
- Roles
- Permissions
- Enrolment
- Course content
- Quiz and assessment
- Gradebook
- Assignments
- File infrastructure
- Messaging
- Logging
- Course completion

Training Portal must **extend Moodle rather than duplicate Moodle**.

---

# 2. Primary Goal

The first release must provide one complete working vertical workflow:

```text
Applicant
    ↓
Selects a Training Programme
    ↓
Completes Registration
    ↓
Uploads Payment Proof
    ↓
Application Created
    ↓
Coordinator Reviews Application
    ↓
Coordinator Approves
    ↓
Moodle Account Created / Existing Account Linked
    ↓
Participant Enrolled in Moodle Course
    ↓
Participant Can Access Course
    ↓
Coordinator Issues Certificate
    ↓
Certificate Available
```

This entire flow must work before additional functionality such as attendance, project verification, automatic eligibility, analytics, or AI features is introduced.

---

# 3. Project Scope

## 3.1 Sprint 1 Scope

Sprint 1 includes:

- Training offering management
- Public registration
- Participant information collection
- Course/training selection
- Payment reference collection
- Payment proof upload
- Application number generation
- Application status management
- Coordinator application dashboard
- Payment-proof viewing
- Application approval
- Application rejection
- Moodle user lookup
- Moodle user creation
- Moodle user linking
- Moodle course enrolment
- Participant notification
- Certificate issuance by coordinator
- Certificate record
- Certificate PDF generation
- Basic audit events
- Permissions and capabilities
- Basic automated tests

---

## 3.2 Explicitly Out of Scope for Sprint 1

Do not implement the following during Sprint 1:

- Attendance tracking
- QR attendance
- Biometric attendance
- Test eligibility rules
- Grade-based certificate eligibility
- Project submission
- Project approval
- Project rubric
- AI project analysis
- Automatic certificate eligibility
- Training analytics
- Advanced reporting
- Payment gateway integration
- Online payment processing
- Refund workflow
- SMS integration
- WhatsApp integration
- Mobile application
- React frontend
- FastAPI backend
- Separate REST service
- Separate Training Portal database
- Redis
- Kafka
- Microservices
- Kubernetes

These may be added in later releases.

---

# 4. Architectural Principle

Training Portal is a Moodle plugin.

It is **not** an independent web application.

```text
                    Browser
                       │
                       ▼
               Moodle Web Server
                       │
         ┌─────────────┴─────────────┐
         │                           │
         ▼                           ▼
  Moodle Core                 Training Portal
                              local plugin
         │                           │
         └─────────────┬─────────────┘
                       │
                       ▼
                 Moodle Database
```

The plugin executes inside the Moodle PHP runtime.

There is no separate Training Portal server.

There is no:

```text
Training Portal API
        ↓
REST
        ↓
Moodle
```

during Sprint 1.

Instead:

```text
Training Portal PHP
        ↓
Moodle APIs
        ↓
Moodle Core
```

---

# 5. Component Name

Plugin type:

```text
local
```

Plugin name:

```text
trainingportal
```

Moodle component name:

```text
local_trainingportal
```

Plugin location for the target Moodle installation:

```text
public/local/trainingportal/
```

Repository:

```text
git@github.com:Neo017/trainingportal.git
```

---

# 6. Development Architecture

Recommended development setup:

```text
Machine A
Development Workstation

├── VS Code / IDE
├── Coding Model
├── Git
└── SSH
      │
      ▼

Machine B
Moodle Development Server

~/training-system/
├── moodle/
│   └── public/
│       └── local/
│           └── trainingportal/
│
└── moodle-docker/
```

Machine B executes Moodle.

Machine A can either:

1. Edit code through Remote SSH, or
2. Develop locally and push to GitHub, followed by a pull on Machine B.

Remote development is recommended because the coding environment can inspect both:

```text
Moodle source
+
Training Portal source
```

This reduces incorrect assumptions about Moodle APIs.

---

# 7. Containerisation Strategy

## 7.1 Development Environment

Docker should be used for the Moodle development environment.

Docker may provide:

```text
Moodle/PHP
PostgreSQL
Web server
Mail testing service
```

Training Portal itself is **not a separate container**.

Correct:

```text
Docker Development Environment
│
├── Moodle/PHP
├── PostgreSQL
└── Mail
        │
        ▼
public/local/trainingportal/
```

Incorrect:

```text
Docker
├── Moodle
├── PostgreSQL
└── Training Portal API
```

There is no need for a dedicated Training Portal container.

---

# 8. Repository Structure

Target structure:

```text
trainingportal/
│
├── amd/
│   └── src/
│
├── classes/
│   │
│   ├── event/
│   │   ├── application_created.php
│   │   ├── application_approved.php
│   │   ├── application_rejected.php
│   │   └── certificate_issued.php
│   │
│   ├── form/
│   │   ├── registration_form.php
│   │   ├── offering_form.php
│   │   └── rejection_form.php
│   │
│   ├── local/
│   │   └── certificate/
│   │       ├── provider_interface.php
│   │       └── simple_pdf_provider.php
│   │
│   ├── output/
│   │   ├── renderer.php
│   │   ├── applications_page.php
│   │   └── application_detail.php
│   │
│   ├── privacy/
│   │   └── provider.php
│   │
│   └── service/
│       ├── application_service.php
│       ├── approval_service.php
│       ├── certificate_service.php
│       ├── enrolment_service.php
│       ├── notification_service.php
│       ├── offering_service.php
│       └── user_service.php
│
├── db/
│   ├── access.php
│   ├── install.xml
│   └── upgrade.php
│
├── lang/
│   └── en/
│       └── local_trainingportal.php
│
├── pix/
│
├── templates/
│   ├── applications.mustache
│   ├── application_detail.mustache
│   ├── certificate_status.mustache
│   ├── offering_card.mustache
│   └── offerings.mustache
│
├── tests/
│   ├── application_service_test.php
│   ├── approval_service_test.php
│   ├── certificate_service_test.php
│   └── enrolment_service_test.php
│
├── application.php
├── applications.php
├── approve.php
├── certificate.php
├── index.php
├── issue_certificate.php
├── lib.php
├── offerings.php
├── register.php
├── reject.php
├── settings.php
├── version.php
│
├── README.md
├── DEVELOPMENT.md
├── INSTALL.md
└── .gitignore
```

Not all files have to be created immediately.

Create them as their corresponding feature is implemented.

---

# 9. Separation of Responsibilities

## Moodle Core

Moodle owns:

```text
Users
Authentication
Password reset
Sessions
Courses
Roles
Capabilities
Enrolment
Course activities
Quiz
Grades
Assignments
Files infrastructure
Messaging
Logging
Course completion
```

## Training Portal

Training Portal owns:

```text
Training offerings
Applications
Payment proof workflow
Application statuses
Coordinator review
Approval
Rejection
Moodle account linking orchestration
Enrolment orchestration
Certificate issuance workflow
Training-specific audit events
```

---

# 10. Important Domain Model: Course vs Training Offering

A Moodle course and a training programme instance must be treated separately.

Example Moodle course:

```text
Deep Linux and Operating Systems
```

This course may be conducted multiple times:

```text
Deep Linux — September 2026
Deep Linux — November 2026
Deep Linux — February 2027
```

Therefore:

```text
Moodle Course
      │
      ├── Training Offering A
      ├── Training Offering B
      └── Training Offering C
```

A **Training Offering** represents one delivery/batch of a Moodle course.

It can contain:

- Registration dates
- Training dates
- Fee
- Capacity
- Coordinator
- Display name
- Batch code
- Active/inactive state

This prevents batch-specific information from being stored directly against a Moodle course.

---

# 11. Data Model

The plugin initially requires three main tables.

---

# 11.1 Training Offering

Logical table:

```text
local_tp_offering
```

Moodle will apply the configured database prefix.

Fields:

| Field | Purpose |
|---|---|
| `id` | Primary key |
| `courseid` | Moodle course ID |
| `code` | Unique offering/batch code |
| `title` | Public training title |
| `registrationstart` | Registration opening timestamp |
| `registrationend` | Registration closing timestamp |
| `trainingstart` | Training starting timestamp |
| `trainingend` | Training ending timestamp |
| `fee` | Fee amount |
| `capacity` | Maximum participants |
| `active` | Whether offering is active |
| `timecreated` | Created timestamp |
| `timemodified` | Modified timestamp |

Example:

```text
id: 12

courseid: 7

code:
LINUX-SEP26

title:
Deep Linux — September 2026

registrationstart:
...

registrationend:
...

trainingstart:
...

trainingend:
...

fee:
2500

capacity:
40

active:
1
```

---

# 11.2 Training Application

Logical table:

```text
local_tp_application
```

Fields:

| Field | Purpose |
|---|---|
| `id` | Internal primary key |
| `offeringid` | Training offering |
| `applicationcode` | Public application identifier |
| `firstname` | Applicant first name |
| `lastname` | Applicant last name |
| `email` | Applicant email |
| `phone` | Applicant phone |
| `organisation` | Organisation/institution |
| `designation` | Applicant designation |
| `paymentreference` | Payment/transaction reference |
| `paymentstatus` | Payment verification status |
| `status` | Application status |
| `userid` | Linked Moodle user ID |
| `approvedby` | Moodle user ID of coordinator |
| `approvedat` | Approval timestamp |
| `rejectedby` | Moodle user ID |
| `rejectedat` | Rejection timestamp |
| `rejectionreason` | Rejection explanation |
| `timecreated` | Creation timestamp |
| `timemodified` | Modification timestamp |

---

# 11.3 Certificate

Logical table:

```text
local_tp_certificate
```

Fields:

| Field | Purpose |
|---|---|
| `id` | Primary key |
| `applicationid` | Source application |
| `offeringid` | Training offering |
| `courseid` | Moodle course |
| `userid` | Moodle participant |
| `certificatenumber` | Public certificate number |
| `issuedby` | Coordinator Moodle user ID |
| `timeissued` | Issue timestamp |
| `status` | Certificate status |
| `timecreated` | Record creation |
| `timemodified` | Last modification |

Possible future fields:

```text
verificationtoken
revokedby
revokedat
revocationreason
replacementcertificateid
```

---

# 12. Application State Model

Initial states:

```text
PENDING
APPROVED
REJECTED
```

Flow:

```text
             ┌───────────┐
             │  PENDING  │
             └─────┬─────┘
                   │
            ┌──────┴───────┐
            ▼              ▼
       APPROVED         REJECTED
```

Allowed transitions:

```text
PENDING → APPROVED

PENDING → REJECTED
```

Do not permit arbitrary transitions such as:

```text
REJECTED → APPROVED
```

without a separately defined reopen/reconsider workflow.

That can be added later.

---

# 13. Payment State Model

Payment status is independent of application status.

Initial payment states:

```text
UPLOADED
VERIFIED
INVALID
```

Possible application:

```text
Application:
PENDING

Payment:
UPLOADED
```

After review:

```text
Application:
PENDING

Payment:
VERIFIED
```

After approval:

```text
Application:
APPROVED

Payment:
VERIFIED
```

Do not combine these states into values such as:

```text
PAYMENT_VERIFIED_AND_APPROVED
```

Keep business concepts independent.

---

# 14. Certificate State Model

Initial states:

```text
ISSUED
REVOKED
```

Sprint 1 UI only needs issuance.

Revocation support may be introduced later.

Application and certificate state must remain separate.

Example:

```text
Application:
APPROVED

Certificate:
NOT ISSUED
```

Later:

```text
Application:
APPROVED

Certificate:
ISSUED
```

---

# 15. Registration Workflow

Public entry point:

```text
/local/trainingportal/register.php
```

Optional offering-specific URL:

```text
/local/trainingportal/register.php?offering=12
```

Flow:

```text
Open Registration
      ↓
Resolve Training Offering
      ↓
Verify Offering Active
      ↓
Verify Registration Window
      ↓
Display Registration Form
      ↓
Applicant Completes Details
      ↓
Upload Payment Proof
      ↓
Server-side Validation
      ↓
Check Duplicate Application
      ↓
Create Application
      ↓
Store Payment Proof
      ↓
Generate Application Number
      ↓
Display Confirmation
```

---

# 16. Registration Form

Initial fields:

```text
Training Programme
First Name
Last Name
Email
Mobile Number
Organisation
Designation
Payment Reference Number
Payment Proof
Consent/Declaration
```

Potential later fields:

```text
Address
District
Qualification
Experience
Food preference
Accommodation requirement
Gender
Accessibility requirements
Nomination information
```

Do not add these until required.

---

# 17. Registration Validation

Validate all input server-side.

Examples:

### Training Offering

Must:

- Exist
- Be active
- Accept registrations
- Not be outside the registration period

### First/Last Name

Must:

- Be present as required
- Meet reasonable length limits

### Email

Must:

- Be syntactically valid
- Be normalized consistently

### Phone

Must:

- Be validated to the required local format
- Not be trusted solely because browser validation succeeded

### Payment Reference

Must:

- Be present when payment is mandatory
- Respect maximum length

### Payment Proof

Allowed initial formats:

```text
PDF
JPEG
PNG
```

Configure:

- MIME validation
- Extension validation
- Maximum file size
- Maximum one payment proof unless requirements change

Never trust the browser-provided MIME value alone.

---

# 18. Duplicate Applications

For Sprint 1, duplicate detection can use:

```text
same offering
+
same normalized email
+
existing non-rejected application
```

Example:

```text
offering = 12
email = participant@example.com
```

Existing:

```text
status = PENDING
```

Result:

```text
Do not create another application.
```

The participant should be informed that an application already exists.

Future versions may support:

```text
application resubmission
payment-proof replacement
application edits
```

---

# 19. Application Number

Never expose the internal database `id` as the only public identifier.

Internal:

```text
id = 153
```

Public:

```text
TRN-2026-000153
```

Possible format:

```text
TRN-{YEAR}-{SEQUENCE}
```

Example:

```text
TRN-2026-000153
```

The generation mechanism must be deterministic and collision-safe.

---

# 20. Public Registration and Moodle Users

Registration must not create a Moodle user immediately.

Correct:

```text
Registration
      ↓
Application
      ↓
Review
      ↓
Approval
      ↓
Moodle Account
```

Incorrect:

```text
Registration
      ↓
Moodle Account
      ↓
Approval
```

Reasons:

- Rejected applicants should not become Moodle users.
- Payment verification happens before enrolment.
- User database remains cleaner.
- Application lifecycle remains independent from authentication.

---

# 21. Payment Proof Storage

Payment proof files must use Moodle's file infrastructure.

Do not store:

```text
/var/www/uploads/payment.jpg
```

Do not create:

```text
trainingportal/uploads/
```

Do not save arbitrary absolute paths in the database.

Use a plugin-owned Moodle file area.

Conceptual file identity:

```text
component:
local_trainingportal

filearea:
paymentproof

itemid:
applicationid
```

Example:

```text
component:
local_trainingportal

filearea:
paymentproof

itemid:
153
```

The application table does not need to know the underlying physical file path.

---

# 22. Payment File Security

Payment proofs may contain personally identifiable or financial information.

Requirements:

- Never expose payment files publicly.
- Require an authenticated coordinator.
- Require the appropriate capability.
- Force safe download/display behavior for untrusted uploads.
- Never create guessable public direct filesystem URLs.
- Log or otherwise make sensitive access auditable where appropriate.
- Validate file size.
- Validate type.
- Never execute uploaded content.

Access concept:

```text
Request Payment File
       ↓
Moodle Authentication
       ↓
Capability Check
       ↓
Application Exists?
       ↓
File Belongs to Application?
       ↓
Serve Through Moodle
```

---

# 23. Training Offering Management

Coordinator route:

```text
/local/trainingportal/offerings.php
```

Coordinator should be able to:

- List offerings
- Create offering
- Edit offering
- Activate/deactivate offering
- Link offering to Moodle course
- Configure registration dates
- Configure training dates
- Configure fee
- Configure capacity
- Open application list for offering

Example:

```text
TRAINING OFFERINGS

Deep Linux — September 2026

Course:
Deep Linux and Operating Systems

Registration:
OPEN

Dates:
21–25 September 2026

Fee:
₹2500

Capacity:
40

[Applications] [Edit]
```

---

# 24. Coordinator Application Dashboard

Route:

```text
/local/trainingportal/applications.php
```

Required filters:

```text
Training Offering
Status
Payment Status
Search
```

Initial columns:

```text
Application Number
Applicant
Organisation
Offering
Payment Status
Application Status
Submitted Date
Actions
```

Example:

```text
APPLICATIONS

Offering:
[Deep Linux — September 2026 ▼]

Status:
[Pending ▼]

Search:
[                        ]

---------------------------------------------------------

TRN-2026-00121
Anjali Nair
College of Engineering
Payment: Uploaded
Status: Pending

[View]
```

---

# 25. Application Detail Screen

Route:

```text
/local/trainingportal/application.php?id=...
```

Display:

```text
Application Number

Applicant Name
Email
Phone
Organisation
Designation

Training Offering

Payment Reference

Payment Status
[Uploaded / Verified / Invalid]

Payment Proof
[View]

Application Status

Submission Date
```

Available coordinator actions:

```text
Verify Payment
Mark Payment Invalid
Approve
Reject
```

Actions shown must depend on:

- Current state
- User capability
- Payment state
- Application state

---

# 26. Approval Preconditions

An application should only be approved when:

```text
Application exists
AND
Application status = PENDING
AND
Offering exists
AND
Payment requirements satisfied
AND
Current user has approval capability
```

If payment verification is mandatory:

```text
paymentstatus must equal VERIFIED
```

before approval.

---

# 27. Approval Workflow

Coordinator action:

```text
APPROVE
```

Backend:

```text
Receive Application ID
       ↓
Require Coordinator Login
       ↓
Check CSRF/session key
       ↓
Check Capability
       ↓
Load Application
       ↓
Validate Current State
       ↓
Validate Payment Status
       ↓
Resolve Moodle User
       ↓
Create User if Necessary
       ↓
Resolve Moodle Course
       ↓
Resolve Manual Enrolment Instance
       ↓
Enrol Moodle User
       ↓
Update Application
       ↓
Trigger Approval Event
       ↓
Send Notification
       ↓
Redirect to Result
```

---

# 28. Approval Must Be Idempotent

Approval must be safe if accidentally executed more than once.

Example:

### First request

```text
Application:
PENDING

User:
not found
```

Operation:

```text
create Moodle user
enrol user
mark APPROVED
```

### Second request

Application:

```text
APPROVED
```

The service must not:

- Create another Moodle user
- Create a duplicate enrolment
- Send duplicate irreversible actions unnecessarily
- Corrupt state

It should return an appropriate already-approved result.

Idempotency is mandatory.

---

# 29. Moodle User Resolution

Approval service must not create duplicate Moodle users unnecessarily.

Algorithm:

```text
Normalize Applicant Email
       ↓
Look for Existing Moodle User
       ↓
       ├──── Found
       │       ↓
       │    Use Existing userid
       │
       └──── Not Found
               ↓
           Create Moodle User
               ↓
           Receive userid
```

The resulting Moodle `userid` must be stored in:

```text
local_tp_application.userid
```

---

# 30. Moodle User Creation Rules

Do not:

- Insert directly into the Moodle user table
- Maintain a separate password table
- Generate permanent hardcoded passwords
- Send plaintext passwords
- Create a custom authentication implementation for Sprint 1

Use Moodle-supported account creation and authentication processes.

Account creation should supply only the required profile information.

Possible mapping:

```text
firstname → Moodle firstname
lastname  → Moodle lastname
email     → Moodle email
```

Other information should remain in Training Portal unless required in Moodle.

---

# 31. Course Enrolment

Training Portal must use Moodle's enrolment mechanisms.

Do not directly manipulate:

```text
user_enrolments
role_assignments
```

Enrolment workflow:

```text
Training Offering
       ↓
courseid
       ↓
Find Course
       ↓
Find Manual Enrolment Instance
       ↓
Resolve Student Role
       ↓
Enrol Moodle User
```

The Moodle course must have an appropriate enrolment method enabled.

If no compatible enrolment method exists:

```text
Approval must fail safely
```

Do not leave application state showing:

```text
APPROVED
```

when required enrolment failed unless the implementation explicitly supports an intermediate recovery state.

---

# 32. Transaction and Failure Handling

Approval consists of several dependent operations.

Potential failures:

```text
Moodle user creation fails
Course missing
Enrolment instance missing
Enrolment fails
Database update fails
Notification fails
```

Critical state-changing operations should be designed so that partial failures are recoverable.

Do not create situations such as:

```text
Application says approved
BUT
participant was never enrolled
```

Where possible, use database transactions for local database state.

External/secondary operations such as notification should not necessarily invalidate a successful enrolment.

Example:

```text
Core approval + enrolment:
SUCCESS

Email:
FAILED

Result:
Approval remains valid.
Notification failure is logged/retryable.
```

---

# 33. Rejection Workflow

Coordinator:

```text
REJECT
```

Requires:

```text
reason
```

Flow:

```text
Require Login
      ↓
Capability Check
      ↓
CSRF Check
      ↓
Load Application
      ↓
Validate PENDING State
      ↓
Capture Rejection Reason
      ↓
Update Application
      ↓
Trigger Event
      ↓
Notify Applicant
```

Store:

```text
status = REJECTED
rejectedby
rejectedat
rejectionreason
```

Rejected applicants must not be automatically:

- Created as Moodle users
- Enrolled in Moodle
- Issued certificates

---

# 34. Capabilities

Define custom capabilities in:

```text
db/access.php
```

Initial capabilities:

```text
local/trainingportal:manageofferings

local/trainingportal:viewapplications

local/trainingportal:viewpaymentproof

local/trainingportal:verifypayment

local/trainingportal:approveapplications

local/trainingportal:rejectapplications

local/trainingportal:issuecertificates
```

Potential future:

```text
local/trainingportal:manageattendance
local/trainingportal:reviewprojects
local/trainingportal:viewreports
```

---

# 35. Roles

Do not require coordinators to be Moodle Site Administrators.

Create/configure a suitable role such as:

```text
Training Coordinator
```

Assign only necessary capabilities.

Conceptually:

| Capability | Coordinator |
|---|---|
| Manage offerings | Allow |
| View applications | Allow |
| View payment proof | Allow |
| Verify payment | Allow |
| Approve applications | Allow |
| Reject applications | Allow |
| Issue certificates | Allow |

This follows least-privilege principles.

---

# 36. Authorization

Every administrative page must perform authorization.

General pattern:

```text
Set Page URL
      ↓
Require Login
      ↓
Resolve Moodle Context
      ↓
Require Capability
      ↓
Perform Operation
```

Never authorize using:

```text
username == 'admin'
```

Never authorize using:

```text
userid == 2
```

Never depend on hidden buttons as security.

Server-side capability checks are mandatory.

---

# 37. CSRF Protection

All state-changing operations require Moodle session-key protection.

Examples:

```text
Approve
Reject
Verify Payment
Issue Certificate
Create Offering
Update Offering
```

GET requests should not perform destructive/state-changing operations by themselves.

Bad:

```text
/approve.php?id=13
```

causes approval immediately.

Preferred concept:

```text
GET:
show confirmation

POST:
validate session key
execute approval
```

---

# 38. Service Layer

Business logic must live in classes.

Do not place large amounts of logic in page controllers.

Bad:

```text
approve.php

300 lines containing:
user creation
database updates
enrolment
notifications
file handling
```

Preferred:

```text
approve.php
      ↓
approval_service
      ↓
user_service
      ↓
enrolment_service
      ↓
notification_service
```

Page controllers should:

1. Bootstrap Moodle
2. Parse parameters
3. Check authorization
4. Call service
5. Render/redirect

---

# 39. Service Responsibilities

## `offering_service.php`

Responsible for:

- Create offering
- Update offering
- Retrieve offering
- List offerings
- Validate registration period
- Validate Moodle course
- Check capacity
- Activate/deactivate

---

## `application_service.php`

Responsible for:

- Validate registration eligibility
- Duplicate detection
- Create application
- Generate application code
- Retrieve application
- List/filter applications
- Update payment status
- Application state validation

---

## `user_service.php`

Responsible for:

- Normalize user identifier
- Find Moodle user
- Create Moodle user
- Return Moodle `userid`

---

## `enrolment_service.php`

Responsible for:

- Validate Moodle course
- Find enrolment instance
- Resolve participant/student role
- Check existing enrolment
- Enrol user
- Ensure duplicate enrolment is not created

---

## `approval_service.php`

Responsible for orchestration:

```text
Validate Application
       ↓
Validate Payment
       ↓
Resolve/Create User
       ↓
Enrol User
       ↓
Update Application
       ↓
Trigger Event
       ↓
Notify
```

---

## `notification_service.php`

Responsible for messages such as:

```text
Application submitted
Application approved
Application rejected
Certificate issued
```

Notification templates/content should not be embedded throughout unrelated classes.

---

## `certificate_service.php`

Responsible for:

- Validate certificate eligibility for Sprint 1
- Check that application is approved
- Check Moodle user link
- Prevent duplicate issuance
- Generate certificate number
- Record issuing coordinator
- Generate PDF
- Store PDF
- Return certificate/download information
- Trigger certificate event

---

# 40. Certificate Strategy

For Sprint 1, certificate generation should remain simple.

Coordinator manually issues the certificate.

Flow:

```text
Approved Participant
       ↓
Coordinator Opens Participant
       ↓
Issue Certificate
       ↓
Validate Application
       ↓
Check Existing Certificate
       ↓
Generate Certificate Number
       ↓
Create PDF
       ↓
Store PDF
       ↓
Record Issuance
       ↓
Certificate Available
```

Future eligibility logic must be able to sit before issuance without rewriting certificate generation.

---

# 41. Certificate Provider Abstraction

Use an abstraction such as:

```text
certificate_service
       ↓
certificate provider
```

Interface:

```text
provider_interface
```

Initial provider:

```text
simple_pdf_provider
```

Possible future provider:

```text
customcert_provider
```

This prevents business logic from depending directly on one PDF implementation.

---

# 42. Certificate Number

Certificate number must be unique.

Example:

```text
ICFOSS-LINUX-2026-00042
```

Possible components:

```text
organisation prefix
offering/course code
year
sequence
```

Do not depend on certificate filename as identity.

Database uniqueness should enforce certificate-number uniqueness.

---

# 43. Certificate Content

Initial certificate may include:

```text
Organisation name/logo
Certificate heading
Participant name
Training title
Training dates
Duration
Certificate number
Issue date
Coordinator/authority
```

Potential later additions:

```text
QR code
Verification URL
Digital signature
Competencies
Grade
Attendance percentage
Project information
```

Do not introduce these until required.

---

# 44. Certificate Issuance Rules — Sprint 1

Sprint 1 eligibility:

```text
Application = APPROVED
AND
Moodle userid exists
AND
Participant enrolment exists
AND
Coordinator has issue capability
```

Later:

```text
Application = APPROVED
AND
Attendance >= configured threshold
AND
Moodle test >= configured threshold
AND
Project = APPROVED
```

The future eligibility engine should be inserted before certificate issuance.

---

# 45. Future Certificate Eligibility Architecture

Sprint 2:

```text
Attendance
      │
      ├───────────┐
      │           │
Moodle Test    Project
      │           │
      └─────┬─────┘
            ▼
      Eligibility Service
            │
       Eligible?
        /       \
      NO         YES
      │           │
      ▼           ▼
 Show reason    Enable
              Certificate
```

This must not require redesigning registration or enrolment.

---

# 46. Events and Audit Trail

Define Moodle events for significant actions.

Initial events:

```text
application_created
application_approved
application_rejected
certificate_issued
```

Optional:

```text
payment_verified
payment_marked_invalid
offering_created
offering_updated
```

Events should capture useful context without leaking unnecessary sensitive data.

Example audit concept:

```text
2026-09-10 10:32
TRN-2026-00128 submitted

2026-09-10 13:10
Payment verified by coordinator

2026-09-10 13:14
Application approved

2026-09-10 13:14
Moodle user linked: userid 481

2026-09-10 13:14
Participant enrolled in course 17

2026-09-15 16:40
Certificate issued
ICFOSS-LINUX-2026-00042
```

Do not create a large custom logging subsystem when Moodle's event/logging infrastructure is appropriate.

---

# 47. Notifications

Sprint 1 notifications:

## Application Submitted

Inform applicant:

```text
Application received
Application number
Training name
Current status
```

---

## Application Approved

Inform participant:

```text
Application approved
Training name
Moodle access information
Course access instructions
```

Do not send plaintext permanent passwords.

---

## Application Rejected

Inform applicant:

```text
Application rejected
Training name
Reason where appropriate
```

---

## Certificate Issued

Inform participant:

```text
Certificate issued
Training name
Certificate number
How to access/download
```

---

# 48. Rendering and UI

Training Portal should look like part of Moodle.

Use:

```text
Moodle page infrastructure
Moodle output/rendering
Mustache templates
Moodle-compatible CSS
Moodle JavaScript modules where required
```

Do not build an unrelated Bootstrap/React application inside the plugin for Sprint 1.

---

# 49. Page Controllers

Expected primary endpoints:

```text
index.php
```

Plugin landing page.

---

```text
offerings.php
```

Training offering management.

---

```text
register.php
```

Public registration.

---

```text
applications.php
```

Coordinator application list.

---

```text
application.php
```

Application detail.

---

```text
approve.php
```

Approval action/confirmation.

---

```text
reject.php
```

Rejection action/form.

---

```text
issue_certificate.php
```

Certificate issuance.

---

```text
certificate.php
```

Certificate view/download handler as required.

---

# 50. Language Strings

Do not hardcode user-facing text throughout PHP.

Use:

```text
lang/en/local_trainingportal.php
```

Examples:

```text
pluginname
registration
applications
application
approve
reject
paymentproof
paymentreference
certificate
certificateissued
```

This enables localization later.

Malayalam language support can later be added under:

```text
lang/ml/
```

without changing application logic.

---

# 51. Database Access

Use Moodle's DML layer.

Do not create:

```text
PDO connection
mysqli connection
PostgreSQL connection
```

inside Training Portal.

All database operations should use Moodle's database abstraction.

Conceptually:

```php
global $DB;
```

Operations should be through Moodle APIs such as:

```text
insert_record
update_record
get_record
get_records
record_exists
delete_records
```

as appropriate.

---

# 52. Database Naming

Keep Moodle plugin table names concise.

Recommended:

```text
local_tp_offering
local_tp_application
local_tp_certificate
```

rather than extremely long names.

Use appropriate indexes and uniqueness constraints.

Important indexes likely include:

```text
applicationcode
offeringid
email
status
paymentstatus
userid
certificatenumber
```

---

# 53. Installation Schema

Initial schema belongs in:

```text
db/install.xml
```

This file represents the schema for a fresh plugin installation.

Once the plugin is installed and schema changes occur later, use:

```text
db/upgrade.php
```

plus an increased plugin version.

Do not expect editing `install.xml` alone to alter databases where the plugin is already installed.

---

# 54. Version Management

`version.php` must contain valid Moodle plugin metadata.

Every database/schema upgrade must increment the plugin version.

Development process:

```text
Change requiring DB migration
       ↓
Update db/upgrade.php
       ↓
Increment version
       ↓
Deploy code
       ↓
Run Moodle upgrade
```

---

# 55. Privacy

Training Portal stores personal information:

```text
Name
Email
Phone
Organisation
Designation
Payment reference
Payment proof
Application status
```

Therefore privacy support must be considered part of the plugin design.

Create:

```text
classes/privacy/provider.php
```

as required by Moodle's privacy framework.

Data-retention policy should eventually be decided institutionally, especially for payment proofs.

Avoid storing personal information that is not required by the workflow.

---

# 56. Security Requirements

## Mandatory

- Server-side validation
- Moodle capabilities
- Authentication for administrative screens
- Session-key/CSRF checks
- Secure file serving
- Restricted payment-proof access
- Output escaping
- Safe redirects
- Parameter validation
- No direct core-table manipulation
- No plaintext passwords
- No hidden-field-based authorization
- No trust in client-side validation
- No publicly guessable payment file paths

---

# 57. Sensitive File Rules

Payment proofs must be treated as untrusted uploads.

Do not:

```text
execute
include
eval
render arbitrary HTML
```

from uploaded content.

Where possible, serve untrusted documents using safe download behavior.

---

# 58. Logging Rules

Do not log:

```text
passwords
full payment document content
authentication secrets
session IDs
```

Log identifiers instead:

```text
application ID
application code
Moodle userid
courseid
offeringid
certificate number
actor userid
```

---

# 59. Error Handling

Users should not see raw PHP exceptions or database details.

Public-facing errors should be understandable.

Example:

```text
Registration could not be completed.
Please verify the submitted information and try again.
```

Coordinator-facing error:

```text
The participant could not be enrolled because manual enrolment
is not configured for this Moodle course.
```

Developer logs may contain additional technical context.

---

# 60. Coding Model Rules

Every coding-model task must begin with the following constraints.

```text
Project: Training Portal
Component: local_trainingportal
Plugin type: Moodle local plugin
Target: Moodle 5.2

Rules:

1. Do not modify Moodle core.

2. Follow Moodle plugin conventions and Moodle coding style.

3. Inspect the locally installed Moodle source before assuming
   an API signature or internal implementation.

4. Use Moodle DML APIs for database operations.

5. Use Moodle File API for persistent plugin-owned files.

6. Use Moodle Forms API where appropriate.

7. Use Moodle Access API and custom capabilities.

8. Use Moodle-supported user APIs for Moodle account creation.

9. Use Moodle enrolment APIs for course enrolment.

10. Never write directly to core Moodle tables.

11. Keep page controllers thin.

12. Place business logic in namespaced classes under classes/.

13. Use Mustache/rendering infrastructure for presentation where
    appropriate.

14. All authenticated state-changing operations must include
    capability and CSRF/session-key checks.

15. Approval must be idempotent.

16. Certificate issuance must be idempotent.

17. Validate all input server-side.

18. Escape output.

19. Do not introduce React, Laravel, Symfony, FastAPI, Node.js,
    another database, Redis, Kafka, or a microservice.

20. Do not implement functionality outside the requested task.

21. Add or update automated tests for business-critical behavior.

22. Report all files changed.

23. Report any database/schema change.

24. Report the tests executed.

25. Provide manual verification steps after implementation.
```

---

# 61. Coding Model Working Method

Do not request:

```text
"Build the entire Training Portal plugin."
```

Instead:

```text
Task 1:
Create minimal Moodle plugin.

Test it.

Task 2:
Add initial schema.

Test it.

Task 3:
Add capabilities.

Test it.

Task 4:
Add offering management.

Test it.

...
```

One feature should be completed and verified before moving to the next.

---

# 62. Development Stages

## Stage 0 — Minimal Plugin Bootstrap

Goal:

```text
Moodle recognizes Training Portal.
```

Develop:

```text
version.php
lang/en/local_trainingportal.php
index.php
```

Acceptance criteria:

- Moodle detects plugin.
- Installation completes without error.
- Plugin component is `local_trainingportal`.
- Plugin landing page renders using Moodle.

Do not implement registration yet.

---

# 63. Stage 1 — Database Schema

Develop:

```text
db/install.xml
```

Create:

```text
local_tp_offering
local_tp_application
local_tp_certificate
```

Acceptance criteria:

- Fresh installation creates tables.
- Moodle upgrade/install succeeds.
- Required keys/indexes exist.
- Schema uses Moodle-compatible datatypes.

---

# 64. Stage 2 — Capabilities

Develop:

```text
db/access.php
```

Capabilities:

```text
manageofferings
viewapplications
viewpaymentproof
verifypayment
approveapplications
rejectapplications
issuecertificates
```

Acceptance criteria:

- Site administrator can access workflow.
- Normal student cannot access coordinator pages.
- Coordinator role can be configured without granting site administration.

---

# 65. Stage 3 — Training Offering Service

Develop:

```text
classes/service/offering_service.php
classes/form/offering_form.php
offerings.php
```

Features:

- Create offering
- Link to Moodle course
- Edit offering
- Enable/disable
- Registration window
- Training dates
- Fee
- Capacity

Acceptance:

```text
Coordinator can create:

Deep Linux — September 2026

linked to Moodle course:
Deep Linux and Operating Systems
```

---

# 66. Stage 4 — Public Registration

Develop:

```text
classes/form/registration_form.php
register.php
classes/service/application_service.php
```

Acceptance:

- Anonymous applicant can access registration.
- Active offering shown.
- Closed offering cannot receive registration.
- Required fields validated.
- Successful application stored.

---

# 67. Stage 5 — Payment Proof

Develop:

- Payment reference field
- File validation
- Moodle file storage
- Secure file serving
- Coordinator access

Acceptance:

- Applicant can submit supported payment proof.
- Unsupported files rejected.
- Oversized files rejected.
- Payment file is not publicly accessible.
- Authorized coordinator can view file.

---

# 68. Stage 6 — Application Number and Confirmation

Develop:

```text
application code generation
confirmation page
```

Acceptance:

```text
TRN-2026-000001
```

is generated uniquely.

Applicant receives confirmation after successful submission.

---

# 69. Stage 7 — Coordinator Application Dashboard

Develop:

```text
applications.php
application.php
templates/applications.mustache
templates/application_detail.mustache
```

Features:

- Filter by offering
- Filter by application status
- Filter by payment status
- Search applicant
- View details
- View payment proof

Acceptance:

Coordinator can review submitted application without direct database access.

---

# 70. Stage 8 — Payment Verification

Develop:

```text
Verify payment
Mark payment invalid
```

Acceptance:

Coordinator can change:

```text
UPLOADED → VERIFIED
```

or:

```text
UPLOADED → INVALID
```

Audit information should identify the acting user where required.

---

# 71. Stage 9 — Moodle User Integration

Develop:

```text
classes/service/user_service.php
```

Acceptance:

### Existing user

```text
Applicant email matches Moodle user
       ↓
Existing userid reused
```

### New user

```text
No Moodle user
       ↓
Moodle account created
       ↓
userid returned
```

No duplicate Moodle user should be created for repeat approval attempts.

---

# 72. Stage 10 — Moodle Enrolment

Develop:

```text
classes/service/enrolment_service.php
```

Acceptance:

- Course is resolved from offering.
- Existing enrolment detected.
- Compatible/manual enrolment instance found.
- User enrolled.
- Participant appears in Moodle course participants.
- Repeated request does not duplicate enrolment.

---

# 73. Stage 11 — Application Approval

Develop:

```text
classes/service/approval_service.php
approve.php
```

Acceptance:

Given:

```text
Application = PENDING
Payment = VERIFIED
```

Approval produces:

```text
Application = APPROVED
userid = Moodle userid
course enrolment = active
approvedby = coordinator userid
approvedat = timestamp
```

---

# 74. Stage 12 — Rejection

Develop:

```text
classes/form/rejection_form.php
reject.php
```

Acceptance:

```text
PENDING → REJECTED
```

with:

```text
reason
rejectedby
rejectedat
```

No user/enrolment is created as a result of rejection.

---

# 75. Stage 13 — Notifications

Develop:

```text
notification_service.php
```

Initial notifications:

```text
Application Submitted
Application Approved
Application Rejected
Certificate Issued
```

Acceptance:

Messages can be observed using the development mail environment.

---

# 76. Stage 14 — Certificate Foundation

Develop:

```text
provider_interface.php
simple_pdf_provider.php
certificate_service.php
```

Acceptance:

Coordinator can issue certificate to an approved/enrolled participant.

Certificate contains:

```text
Participant
Training title
Dates
Certificate number
Issue date
```

---

# 77. Stage 15 — Certificate UI

Develop:

```text
issue_certificate.php
certificate.php
certificate_status.mustache
```

Acceptance:

Before issuance:

```text
Certificate:
Not Issued

[Issue Certificate]
```

After:

```text
Certificate:
Issued

Certificate No:
ICFOSS-LINUX-2026-00042

[Download Certificate]
```

Repeated clicking must not generate duplicate certificates.

---

# 78. Stage 16 — Events

Develop event classes:

```text
application_created.php
application_approved.php
application_rejected.php
certificate_issued.php
```

Acceptance:

Important workflow operations generate Moodle events visible through appropriate logging mechanisms.

---

# 79. Stage 17 — Privacy and Hardening

Develop:

```text
classes/privacy/provider.php
```

Review:

- Personal data
- Payment file access
- Input validation
- Escaping
- CSRF protection
- Capabilities
- File serving
- Error handling
- Logging

---

# 80. Stage 18 — Automated Tests

Critical tests:

| Test | Expected |
|---|---|
| Valid application | Created |
| Invalid offering | Rejected |
| Closed offering | Rejected |
| Missing name | Rejected |
| Invalid email | Rejected |
| Duplicate application | Prevented |
| Invalid payment type | Rejected |
| Oversized proof | Rejected |
| Unauthorized application list | Denied |
| Unauthorized payment access | Denied |
| Verify payment | Status changes |
| Approve unverified payment | Denied |
| Approve valid application | Success |
| Existing Moodle user | Reused |
| New Moodle user | Created |
| Missing course | Safe failure |
| Missing enrolment method | Safe failure |
| Valid enrolment | Success |
| Duplicate approval | Idempotent |
| Duplicate enrolment | Prevented |
| Reject valid pending application | Success |
| Issue before approval | Denied |
| Issue approved participant certificate | Success |
| Duplicate certificate request | No duplicate |
| Unauthorized certificate request | Denied |

---

# 81. Manual End-to-End Test

Sprint 1 cannot be considered complete until this test succeeds.

## Step 1

Administrator creates Moodle course:

```text
Deep Linux and Operating Systems
```

## Step 2

Coordinator creates offering:

```text
Deep Linux — September 2026
```

linked to that course.

## Step 3

Applicant opens:

```text
/local/trainingportal/register.php
```

## Step 4

Applicant:

- Selects training
- Enters details
- Enters payment reference
- Uploads payment proof
- Submits

## Step 5

System creates:

```text
TRN-2026-000001
```

with:

```text
Application = PENDING
Payment = UPLOADED
```

## Step 6

Coordinator logs into Moodle.

## Step 7

Coordinator opens application.

## Step 8

Coordinator views payment proof.

## Step 9

Coordinator verifies payment.

State:

```text
Payment = VERIFIED
```

## Step 10

Coordinator clicks:

```text
Approve
```

## Step 11

System checks Moodle user.

If missing:

```text
create user
```

## Step 12

System enrols participant.

## Step 13

Application becomes:

```text
APPROVED
```

## Step 14

Participant logs into Moodle.

## Step 15

Participant can access:

```text
Deep Linux and Operating Systems
```

## Step 16

Coordinator opens participant/application.

## Step 17

Coordinator clicks:

```text
Issue Certificate
```

## Step 18

System generates certificate.

## Step 19

Certificate record contains:

```text
certificate number
participant
offering
course
issued by
issue date
```

## Step 20

Certificate PDF can be downloaded.

This completes Sprint 1.

---

# 82. Definition of Done — Sprint 1

Sprint 1 is complete only when:

- [ ] Moodle installs `local_trainingportal`
- [ ] Database schema installs cleanly
- [ ] Coordinator capabilities work
- [ ] Moodle course can be exposed as training offering
- [ ] Public registration works
- [ ] Payment reference can be submitted
- [ ] Payment proof upload works
- [ ] Payment proof is securely stored
- [ ] Application number is generated
- [ ] Duplicate applications are handled
- [ ] Coordinator dashboard works
- [ ] Payment proof can be reviewed
- [ ] Payment can be verified
- [ ] Application can be approved
- [ ] Application can be rejected
- [ ] Existing Moodle account is reused
- [ ] Missing Moodle account is created
- [ ] Approved participant is enrolled
- [ ] Participant can access course
- [ ] Notifications work
- [ ] Coordinator can issue certificate
- [ ] Certificate is unique
- [ ] Duplicate issuance is prevented
- [ ] Important events are logged
- [ ] Unauthorized users cannot access coordinator functionality
- [ ] Critical automated tests pass
- [ ] Full end-to-end manual test passes

---

# 83. Future Sprint 2

Sprint 2 introduces certificate eligibility.

Current:

```text
Coordinator
      ↓
Issue Certificate
```

Future:

```text
Attendance
      +
Moodle Test
      +
Project Verification
      ↓
Eligibility
      ↓
Coordinator
      ↓
Issue Certificate
```

---

# 84. Attendance — Future

Potential architecture:

```text
Participant
      ↓
Training Sessions
      ↓
Attendance Records
      ↓
Attendance %
```

Eligibility example:

```text
Attendance >= 80%
```

This should eventually become configurable per offering.

---

# 85. Moodle Test Integration — Future

Test remains a Moodle activity.

Training Portal should not implement another test system.

```text
Moodle Quiz
     ↓
Moodle Gradebook
     ↓
Training Portal reads required result
```

Example:

```text
Minimum Test Mark:
50%
```

---

# 86. Project Verification — Future

Project status:

```text
NOT_SUBMITTED
SUBMITTED
CHANGES_REQUESTED
APPROVED
REJECTED
```

Possible project workflow:

```text
Submission
     ↓
Evidence
     ↓
Coordinator/Reviewer
     ↓
Rubric
     ↓
Approved
```

Project verification remains human-controlled initially.

---

# 87. Future Eligibility Engine

Example configuration:

```text
Minimum Attendance:
80%

Minimum Moodle Test:
50%

Project Required:
Yes
```

Engine:

```text
attendance_passed
AND
test_passed
AND
project_passed
```

Result:

```text
ELIGIBLE
```

or:

```text
NOT_ELIGIBLE
```

with clear reasons.

---

# 88. Future Participant Dashboard

Potential:

```text
MY TRAINING

Deep Linux — September 2026

Application      ✓ Approved
Payment          ✓ Verified
Enrollment       ✓ Active
Attendance       92%
Test             78%
Project          ✓ Approved
Certificate      ✓ Issued

[Download Certificate]
```

---

# 89. Future Coordinator Dashboard

Potential:

```text
Deep Linux — September 2026

Applications        86
Pending             12
Approved            60
Rejected            14

Participants        60

Attendance Passed   54
Test Passed         51
Project Approved    48

Eligible            45
Certificates        44
```

Do not implement these analytics in Sprint 1.

---

# 90. What Must Never Be Reimplemented

Do not create custom equivalents of:

```text
Moodle Users
Moodle Passwords
Moodle Sessions
Moodle Course Table
Moodle Quiz
Moodle Gradebook
Moodle Role System
Moodle Permission System
Moodle File Storage Infrastructure
```

Training Portal should consume those services.

---

# 91. Technology Stack

## Application

```text
PHP
Moodle Plugin APIs
```

## LMS

```text
Moodle
```

## Database

```text
Moodle-supported relational database
```

Development recommendation:

```text
PostgreSQL
```

## Templates

```text
Mustache
```

## Browser-side Code

```text
Moodle-compatible JavaScript / AMD modules
```

only where required.

## Files

```text
Moodle File API
```

## Authentication

```text
Moodle
```

## Authorization

```text
Moodle Roles and Capabilities
```

## Course Management

```text
Moodle
```

## Enrolment

```text
Moodle Enrolment API
```

---

# 92. What Is Not Required

Sprint 1 does not require:

```text
Python
FastAPI
Flask
Django
React
Next.js
Node.js backend
Laravel
Separate PostgreSQL database
MongoDB
Redis
Kafka
RabbitMQ
Elasticsearch
Kubernetes
GPU
CUDA
LLM
Vector database
```

---

# 93. Git Strategy

Repository:

```text
git@github.com:Neo017/trainingportal.git
```

Main branch:

```text
main
```

Recommended feature branches:

```text
feature/plugin-bootstrap
feature/database-schema
feature/capabilities
feature/training-offerings
feature/registration
feature/payment-upload
feature/application-dashboard
feature/payment-verification
feature/moodle-user-integration
feature/course-enrolment
feature/application-approval
feature/application-rejection
feature/notifications
feature/certificate
feature/audit-events
feature/privacy
```

---

# 94. Commit Style

Prefer focused commits.

Good:

```text
Add initial plugin metadata

Add training offering schema

Implement public registration form

Add secure payment proof storage

Implement Moodle user resolution

Add Moodle course enrolment service

Implement coordinator approval workflow

Add certificate issuance service
```

Avoid:

```text
changes

update

working

final

misc fixes
```

---

# 95. Development Workflow

Example:

```bash
git checkout main

git pull

git checkout -b feature/registration
```

Develop and test.

Then:

```bash
git add .

git commit -m "Implement public training registration"

git push -u origin feature/registration
```

Merge after testing.

---

# 96. Moodle Source Repository

Moodle core and Training Portal remain separate repositories.

Example:

```text
~/training-system/moodle/
    .git/

~/training-system/moodle/public/local/trainingportal/
    .git/
```

Do not commit Training Portal into your Moodle fork.

Do not commit Moodle core into Training Portal.

---

# 97. Developer Environment

Machine B recommended:

```text
Ubuntu Linux
4–8 CPU cores
16 GB RAM
100–200 GB SSD
Git
OpenSSH
Docker
Docker Compose
```

No GPU is required.

---

# 98. Deployment Concept

Development:

```text
Developer
    ↓
Feature Branch
    ↓
GitHub
    ↓
Moodle Development Server
    ↓
Test
```

Production later:

```text
Feature Branch
    ↓
Review
    ↓
main
    ↓
Release Tag
    ↓
Moodle Production
```

Never develop directly against production.

---

# 99. Plugin Upgrade Flow

When code only changes PHP/templates:

```text
Deploy Code
     ↓
Refresh/Test
```

When plugin database structure changes:

```text
Develop Schema Migration
     ↓
Increment Plugin Version
     ↓
Deploy Code
     ↓
Run Moodle Upgrade
     ↓
Verify Schema
```

Always test upgrades on development before production.

---

# 100. Code Review Checklist

Every significant change should be checked for:

### Architecture

- [ ] No Moodle core modification
- [ ] Business logic belongs in a service
- [ ] Moodle APIs used where available
- [ ] No unnecessary framework introduced

### Security

- [ ] Capability check
- [ ] Authentication where required
- [ ] Session-key/CSRF protection
- [ ] Parameter validation
- [ ] Server-side form validation
- [ ] Escaped output
- [ ] Secure file access

### Database

- [ ] Moodle DML API used
- [ ] No direct database connection
- [ ] Correct indexes
- [ ] Correct uniqueness
- [ ] Migration added if required

### Workflow

- [ ] Invalid state transitions prevented
- [ ] Operation idempotent where necessary
- [ ] Partial failure handled
- [ ] Existing Moodle user handled
- [ ] Existing enrolment handled

### Tests

- [ ] Happy path
- [ ] Failure path
- [ ] Permission failure
- [ ] Duplicate operation
- [ ] Invalid input

---

# 101. Architectural Red Flags

Reject code that introduces:

```text
Direct writes to Moodle core tables

Custom password storage

Custom authentication

Public payment-proof URLs

Filesystem paths for persistent Moodle content

Authorization based on user IDs

Business logic embedded in templates

Large business logic inside page scripts

Duplicate course tables

Duplicate Moodle user tables

Separate Training Portal database

Independent PHP framework

Independent API server

React frontend without a justified requirement

Microservices for Sprint 1
```

---

# 102. Development Priority

The order of importance is:

```text
Correctness
    ↓
Security
    ↓
Moodle compatibility
    ↓
Maintainability
    ↓
Testability
    ↓
User experience
    ↓
Additional features
```

Do not sacrifice correctness or Moodle compatibility for a visually sophisticated first release.

---

# 103. First Coding Task

The first coding-model instruction should be:

```text
Create the minimal installable Moodle 5.2 local plugin
local_trainingportal.

Implement only:

- version.php
- lang/en/local_trainingportal.php
- index.php

Requirements:

- Do not modify Moodle core.
- Follow Moodle coding conventions.
- Use component local_trainingportal.
- Render the index page through Moodle's page/output system.
- Do not create database tables yet.
- Do not implement registration yet.
- Do not add external frameworks.

After implementation:

1. List files created or modified.
2. Explain each file.
3. Run available syntax/style checks.
4. Give exact steps to verify that Moodle detects and installs the plugin.
```

Success criterion:

```text
Moodle detects and installs Training Portal.
```

Only after that passes should database development begin.

---

# 104. Second Coding Task

After Stage 0 works:

```text
Implement the initial database schema for local_trainingportal.

Add:

db/install.xml

Tables:

local_tp_offering
local_tp_application
local_tp_certificate

Do not implement UI or business logic yet.

Requirements:

- Follow Moodle XMLDB conventions.
- Add appropriate keys/indexes.
- Application code must be unique.
- Offering code must be unique.
- Certificate number must be unique.
- Reference Moodle course/user IDs logically without directly
  modifying Moodle core.

After implementation:

1. List schema.
2. Explain field choices.
3. Explain indexes.
4. Provide fresh-install verification.
5. Do not implement any feature outside schema creation.
```

---

# 105. Third Coding Task

After schema installation succeeds:

```text
Implement Moodle capabilities for Training Portal.

Add:

db/access.php

Capabilities:

local/trainingportal:manageofferings
local/trainingportal:viewapplications
local/trainingportal:viewpaymentproof
local/trainingportal:verifypayment
local/trainingportal:approveapplications
local/trainingportal:rejectapplications
local/trainingportal:issuecertificates

Do not implement business logic yet.

Explain recommended context and archetypes for each capability.
```

Proceed one stage at a time from there.

---

# 106. Final Sprint 1 Architecture

At Sprint 1 completion:

```text
                         PUBLIC
                           │
                           ▼
                  Training Offering
                           │
                           ▼
                    Registration
                           │
                           ▼
                  Payment Proof
                           │
                           ▼
                    Application
                           │
                      PENDING
                           │
                           ▼
               Coordinator Dashboard
                           │
                  Verify Payment
                           │
                  ┌────────┴────────┐
                  │                 │
                  ▼                 ▼
               Reject            Approve
                                    │
                                    ▼
                              Resolve User
                                    │
                       ┌────────────┴────────────┐
                       │                         │
                       ▼                         ▼
                 Existing User              New User
                       │                         │
                       │                    Create User
                       │                         │
                       └────────────┬────────────┘
                                    │
                                    ▼
                              Moodle userid
                                    │
                                    ▼
                                 Enrol
                                    │
                                    ▼
                              Moodle Course
                                    │
                                    ▼
                              Course Access
                                    │
                                    ▼
                         Coordinator Certificate
                                    │
                                    ▼
                              Generate PDF
                                    │
                                    ▼
                         Certificate Record
```

---

# 107. Core Design Rule

The long-term design principle is:

```text
Training Portal owns
the training workflow.

Moodle owns
the LMS.
```

Whenever a new feature is proposed, first ask:

> Does Moodle already provide this capability?

If yes:

```text
Integrate with Moodle.
```

If no:

```text
Implement it in Training Portal.
```

This keeps the plugin small, maintainable, upgrade-friendly, and suitable for later extension.

---

# 108. Sprint 1 Success Statement

The first release is successful when an applicant who does not yet exist in Moodle can:

```text
Register
    ↓
Upload Payment Proof
    ↓
Receive Application Number
    ↓
Be Reviewed
    ↓
Be Approved
    ↓
Receive/Link Moodle Account
    ↓
Be Enrolled
    ↓
Access the Moodle Course
    ↓
Receive a Coordinator-Issued Certificate
```

with appropriate:

```text
security
permissions
auditability
error handling
Moodle API usage
and automated testing.
```

Everything beyond this is a later iteration.