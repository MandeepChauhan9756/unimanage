<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url(session()->get('role') === 'student' ? 'dashboard' : 'students') ?>" class="text-decoration-none text-muted fs-7 mb-2 d-inline-block">
        <i class="bi bi-arrow-left me-1"></i> <?= session()->get('role') === 'student' ? 'Back to Student Dashboard' : 'Back to Student Directory' ?>
    </a>
    <h3 class="fw-bold text-dark mb-0">Student Profile Summary</h3>
</div>

<?php if (session()->get('role') === 'admin'): ?>
    <?php if ($initialPassword): ?>
        <div class="alert alert-warning">
            <div class="fw-semibold">Temporary student password</div>
            <div class="fs-7 mt-1">Share this with the student securely. It will not be shown again: <code><?= esc($initialPassword) ?></code></div>
        </div>
    <?php endif; ?>
    <div class="card card-soft p-3 mb-4 d-flex flex-row flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="fw-semibold">Student login access</div>
            <div class="text-muted fs-7">Reset the password if the student cannot sign in. The new password will be displayed once.</div>
        </div>
        <form action="<?= base_url('students/reset-password/' . $student['id']) ?>" method="post" onsubmit="return confirm('Reset this student’s login password? The current password will stop working.');">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-key me-1"></i> Reset password</button>
        </form>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left Card: Avatar & Overview -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="mx-auto mb-3 bg-secondary text-white d-flex align-items-center justify-content-center fw-bold fs-1 rounded-circle" style="width: 100px; height: 100px; overflow: hidden;">
                <?php if (!empty($student['avatar']) && $student['avatar'] != 'default.png'): ?>
                    <img src="<?= base_url('uploads/avatars/' . $student['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <h4 class="fw-bold mb-1"><?= esc($student['full_name']) ?></h4>
            <p class="text-muted fs-7 mb-2"><code><?= esc($student['enrollment_no']) ?></code></p>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill mb-3 mx-auto">
                <?= esc($student['department_name']) ?>
            </span>
            <hr>
            <div class="text-start fs-7">
                <p class="mb-2"><strong>Email:</strong> <?= esc($student['email']) ?></p>
                <p class="mb-2"><strong>Phone:</strong> <?= esc($student['phone']) ?></p>
                <p class="mb-2"><strong>Status:</strong> <span class="text-uppercase fw-semibold text-<?= $student['status'] === 'active' ? 'success' : 'danger' ?>"><?= esc($student['status']) ?></span></p>
            </div>
        </div>
    </div>

    <!-- Right Card: Detailed Academic Info -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-semibold text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Academic & Personal Details</h5>
            <div class="row g-3 fs-7">
                <div class="col-md-6">
                    <p class="text-muted mb-1">Department Code</p>
                    <h6 class="fw-bold"><?= esc($student['department_code']) ?></h6>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Course</p>
                    <h6 class="fw-bold"><?= esc($student['course_code']) ?> - <?= esc($student['course_name']) ?></h6>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Current Semester</p>
                    <h6 class="fw-bold">Semester <?= esc($student['semester']) ?> of <?= esc($student['total_semesters']) ?></h6>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Academic Year</p>
                    <h6 class="fw-bold"><?= esc($student['year_label']) ?></h6>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Admission Date</p>
                    <h6 class="fw-bold"><?= esc($student['admission_date']) ?></h6>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Date of Birth</p>
                    <h6 class="fw-bold"><?= esc($student['date_of_birth']) ?></h6>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Gender</p>
                    <h6 class="fw-bold"><?= esc($student['gender']) ?></h6>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Guardian Name</p>
                    <h6 class="fw-bold"><?= esc($student['guardian_name'] ?? 'N/A') ?></h6>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Guardian Phone</p>
                    <h6 class="fw-bold"><?= esc($student['guardian_phone'] ?? 'N/A') ?></h6>
                </div>
                <div class="col-md-12">
                    <p class="text-muted mb-1">Address</p>
                    <h6 class="fw-bold"><?= esc($student['address'] ?? 'N/A') ?></h6>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>