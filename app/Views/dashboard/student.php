<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Student portal</div>
        <h1 class="page-title mb-1">Welcome, <?= esc(session()->get('name')) ?>.</h1>
        <p class="text-muted mb-0">Your university enrollment and student profile at a glance.</p>
    </div>
    <?php if ($student): ?>
        <a href="<?= base_url('students/my-profile') ?>" class="btn btn-primary px-3 py-2"><i class="bi bi-person-vcard me-1"></i> View my profile</a>
    <?php endif; ?>
</div>

<?php if (! $student): ?>
    <section class="card card-soft p-4 p-md-5 text-center">
        <div class="bg-primary-subtle text-primary rounded-4 d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="height:58px;width:58px">
            <i class="bi bi-hourglass-split fs-3"></i>
        </div>
        <h2 class="h4 fw-bold mb-2">Your enrollment is being prepared</h2>
        <p class="text-muted mb-0">Your student login is active, but an enrollment profile is not linked to this account yet. Please contact the university administrator to complete your enrollment.</p>
    </section>
<?php else: ?>
    <?php
        $statusClasses = [
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
            'graduated' => 'primary',
        ];
        $statusClass = $statusClasses[$student['status']] ?? 'secondary';
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <section class="card card-soft h-100 p-3 p-lg-4">
                <div class="text-muted fs-7 mb-2">Outstanding fees</div>
                <div class="h5 fw-bold mb-0">₹<?= esc(number_format((float) ($feeSummary['balance'] ?? 0), 2)) ?></div>
                <a href="<?= base_url('fees/my-fees') ?>" class="fs-7 mt-2 text-decoration-none">View fee details <i class="bi bi-arrow-right"></i></a>
            </section>
        </div>
        <div class="col-md-6 col-xl-3">
            <section class="card card-soft h-100 p-3 p-lg-4">
                <div class="text-muted fs-7 mb-2">Enrollment number</div>
                <div class="h5 fw-bold mb-0"><code><?= esc($student['enrollment_no']) ?></code></div>
            </section>
        </div>
        <div class="col-md-6 col-xl-3">
            <section class="card card-soft h-100 p-3 p-lg-4">
                <div class="text-muted fs-7 mb-2">Course</div>
                <div class="fw-bold"><?= esc($student['course_code']) ?></div>
                <div class="text-muted fs-7"><?= esc($student['course_name']) ?></div>
            </section>
        </div>
        <div class="col-md-6 col-xl-3">
            <section class="card card-soft h-100 p-3 p-lg-4">
                <div class="text-muted fs-7 mb-2">Current semester</div>
                <div class="h5 fw-bold mb-0">Semester <?= esc($student['semester']) ?> of <?= esc($student['total_semesters']) ?></div>
            </section>
        </div>
        <div class="col-md-6 col-xl-3">
            <section class="card card-soft h-100 p-3 p-lg-4">
                <div class="text-muted fs-7 mb-2">Enrollment status</div>
                <span class="badge bg-<?= esc($statusClass) ?>-subtle text-<?= esc($statusClass) ?> border border-<?= esc($statusClass) ?>-subtle px-2 py-1 rounded-pill"><?= esc(ucfirst($student['status'])) ?></span>
            </section>
        </div>
        <div class="col-md-6 col-xl-3">
            <section class="card card-soft h-100 p-3 p-lg-4">
                <div class="text-muted fs-7 mb-2">Attendance rate</div>
                <div class="h5 fw-bold mb-0"><?= (int) ($attendanceStats['total_classes'] ?? 0) > 0 ? esc((string) $attendanceStats['attendance_rate']) . '%' : '—' ?></div>
                <a href="<?= base_url('attendance/my-attendance') ?>" class="fs-7 mt-2 text-decoration-none">View my attendance <i class="bi bi-arrow-right"></i></a>
            </section>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <section class="card card-soft p-3 p-lg-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="h5 fw-bold mb-1">My academic details</h2>
                        <p class="text-muted fs-7 mb-0">Your current enrollment information.</p>
                    </div>
                    <span class="bg-primary-subtle text-primary rounded-3 p-2"><i class="bi bi-mortarboard fs-5"></i></span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted fs-7 mb-1">Department</div>
                        <div class="fw-semibold"><?= esc($student['department_name']) ?> (<?= esc($student['department_code']) ?>)</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted fs-7 mb-1">Academic year</div>
                        <div class="fw-semibold"><?= esc($student['year_label']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted fs-7 mb-1">Admission date</div>
                        <div class="fw-semibold"><?= esc(date('M j, Y', strtotime($student['admission_date']))) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted fs-7 mb-1">Student email</div>
                        <div class="fw-semibold"><?= esc($student['email']) ?></div>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="card card-soft p-3 p-lg-4 h-100">
                <h2 class="h5 fw-bold mb-2">Need help?</h2>
                <p class="text-muted fs-7 mb-3">For corrections to your enrollment, course or personal details, contact your university administrator.</p>
                <a href="<?= base_url('students/my-profile') ?>" class="btn btn-outline-primary mt-auto"><i class="bi bi-person-vcard me-1"></i> Open my full profile</a>
            </section>
        </div>
    </div>
    <section class="card card-soft mt-4">
        <div class="p-3 p-lg-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="h5 fw-bold mb-1">Recent attendance</h2>
                <p class="text-muted fs-7 mb-0"><?= (int) ($attendanceStats['total_classes'] ?? 0) ?> class records · <?= (int) ($attendanceStats['present_count'] ?? 0) + (int) ($attendanceStats['late_count'] ?? 0) ?> attended · <?= (int) ($attendanceStats['absent_count'] ?? 0) ?> absent</p>
            </div>
            <a href="<?= base_url('attendance/my-attendance') ?>" class="btn btn-outline-primary btn-sm">Full attendance history</a>
        </div>
        <?php if ($attendanceRecords === []): ?>
            <div class="p-4 text-center text-muted">Attendance has not been recorded for your enrollment yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th class="ps-4">Date</th><th>Subject</th><th>Academic year</th><th class="pe-4">Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($attendanceRecords as $record): ?>
                        <?php $attendanceColors = ['Present' => 'success', 'Absent' => 'danger', 'Late' => 'warning', 'Excused' => 'secondary']; $attendanceColor = $attendanceColors[$record['status']] ?? 'secondary'; ?>
                        <tr>
                            <td class="ps-4"><?= esc(date('M j, Y', strtotime($record['date']))) ?></td>
                            <td><span class="fw-semibold"><?= esc($record['subject_name']) ?></span> <code class="fs-7"><?= esc($record['subject_code']) ?></code></td>
                            <td><?= esc($record['year_label']) ?></td>
                            <td class="pe-4"><span class="badge bg-<?= esc($attendanceColor) ?>-subtle text-<?= esc($attendanceColor) ?> border border-<?= esc($attendanceColor) ?>-subtle rounded-pill"><?= esc($record['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
<?= $this->endSection() ?>
