# UniManage Admissions CRM

UniManage helps a university counseling and admissions team manage student inquiries from first contact through counseling, application and enrollment. It also retains the existing university records modules for enrolled students, courses, departments and faculty.

## Admissions workflow

1. Sign in as an administrator or counselor and open **Admissions leads**.
2. Add the student's contact information, study preferences, source and preferred university.
3. Assign a counselor, choose a pipeline stage and schedule the next follow-up.
4. Open the student profile to review the admissions details and add call, email, WhatsApp, counseling or application updates to the timeline.
5. Move the lead through **New → Contacted → Counseling → Application** as the student progresses. Update the stage after a conversation and schedule the next follow-up; stage changes are recorded in the timeline.
6. When the application is ready, open the lead and select **Mark Won & Enroll**. Complete the student's account and academic details to create a portal login, generate an enrollment number, add the student to **Enrolled students**, and close the lead as **Won / Enrolled**. The generated initial password is displayed once after conversion and should be shared securely with the student.
7. The dashboard summarizes the pipeline, overdue reminders, enrolled conversions and recent inquiries. Won leads link directly to their student profile.

Pipeline stages are **New**, **Contacted**, **Counseling**, **Application**, **Won / Enrolled** and **Lost**. Leads may be unassigned, and email, location and study preferences are optional so counselors can record an inquiry before every detail is known.

## Student enrollment and records

Administrators can enroll students from **Enrolled students → Add New Student**. Choose an active department, a course in that department, the student's semester and academic year; a profile photo is optional. On success, the student is added to the directory, where staff can search and filter by department, course, year, semester and status, open the student profile, change an active/inactive status, or export the filtered results as CSV. Student accounts have a dashboard and can open their own profile.

Administrators and faculty can mark attendance for an active subject roster by course, semester and academic year, either for the full class or one student at a time. Faculty may record attendance only for assigned subjects; administrators choose an assigned faculty member when saving. Saving an existing student/subject/year/date entry updates that record instead of clearing the whole class session. Students can open **My attendance** to review their own attendance totals, subject summary and filtered history; their portal queries are scoped to the signed-in student's enrollment.

## Fees and accountant workflow

Administrators and accountants can open **Fees & payments** to issue student invoices, set due dates, record full or partial payments, and search/filter invoices by payment status. Each invoice tracks billed, paid and outstanding amounts; overpayments are rejected, and payment method, date, receipt reference and staff member are kept in the payment history. Students can open **My fees** from their portal to see their own invoices, due dates, payment history and balance; they cannot record payments themselves. The accountant dashboard opens the fee workspace directly.

## Product roadmap

- **Current foundation:** counselor lead intake, searchable pipeline, lead ownership, follow-up reminders, stage history and conversation timeline.
- **Partner university operations:** a reusable catalog of partner universities, programs, eligibility criteria, intakes and application deadlines.
- **Lead acquisition:** controlled website intake, campaign attribution and CSV/API integrations with duplicate detection and spam protection.
- **Application-to-enrollment:** lead-to-student conversion is available. Student document checklists and application milestones can be added as a later workflow.
- **Team insights:** counselor workload, response-time, conversion and intake reports; communication integrations can be added when provider credentials and business rules are defined.

## Setup

Requirements: PHP 8.2 or newer, Composer, and a MySQL-compatible database. The application uses CodeIgniter 4.

1. Copy `env` to `.env` and set `app.baseURL` plus the database hostname, database name, username and password.
2. Create the configured database, then apply the schema:

   ```console
   php spark migrate
   ```

3. To create the initial roles, academic reference data and sample accounts, run:

   ```console
   php spark db:seed InitialSeeder
   ```

4. Point the web server document root at `public/`.

The seed file defines local demonstration accounts and passwords. Change or remove those accounts before deploying the application.

For local development, PHP 8.2+ can serve the app directly:

```console
php spark serve --host 127.0.0.1 --port 8080
```

Open `http://localhost:8080/` (the host and port must match `app.baseURL` in `.env`). If using XAMPP Apache, make sure Apache is running PHP 8.2 or newer and that `mod_rewrite` and `AllowOverride All` are enabled for the `public/` directory. Set `app.baseURL` to the URL where the `public/` directory is served. The configured database must also be running and match the `.env` settings.

## Roles

- **Admin**: admissions CRM and university administration.
- **Faculty**: counselor access to leads and university records.
- **Student** and **Accountant**: retained for existing university records workflows; they do not have access to the admissions lead pipeline.

## Testing

Run the application tests with:

```console
composer test
```
