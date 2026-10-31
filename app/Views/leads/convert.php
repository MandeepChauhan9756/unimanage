<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('leads/profile/' . $lead['id']) ?>" class="text-muted text-decoration-none fs-7"><i class="bi bi-arrow-left me-1"></i> Back to lead</a>
    <div class="mt-3">
        <div class="text-uppercase text-success fw-bold fs-7 mb-2">Application complete · Lead won</div>
        <h1 class="page-title mb-1">Enroll <?= esc($lead['student_name']) ?></h1>
        <p class="text-muted mb-0">Complete the academic record to create the student's portal account and enrolled-student profile.</p>
    </div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Please review the enrollment details.</div>
        <ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<section class="card card-soft p-3 p-lg-4 mb-4">
    <div class="d-flex align-items-center gap-3">
        <span class="bg-primary-subtle text-primary rounded-3 p-2"><i class="bi bi-person-vcard fs-5"></i></span>
        <div>
            <h2 class="h6 fw-bold mb-1"><?= esc($lead['student_name']) ?></h2>
            <div class="text-muted fs-7"><?= esc($lead['phone']) ?> · <?= esc($lead['email'] ?: 'Email not provided') ?></div>
        </div>
    </div>
</section>

<form action="<?= base_url('leads/convert/' . $lead['id']) ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-xl-8">
            <section class="card card-soft p-3 p-lg-4 mb-4">
                <h2 class="h5 fw-bold mb-1">Student account</h2>
                <p class="text-muted fs-7 mb-4">The email becomes the student's login ID. A secure one-time password will be shown after enrollment.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="email">Student email <span class="text-danger">*</span></label>
                        <input class="form-control" type="email" id="email" name="email" value="<?= esc(old('email', $lead['email'] ?? '')) ?>" required maxlength="150" autocomplete="email">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="phone">Phone number</label>
                        <input class="form-control" id="phone" value="<?= esc($lead['phone']) ?>" disabled>
                        <div class="form-text">Copied from the admissions lead.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="gender">Gender <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Select gender</option>
                            <?php foreach (['Male', 'Female', 'Other'] as $gender): ?>
                                <option value="<?= esc($gender) ?>" <?= old('gender') === $gender ? 'selected' : '' ?>><?= esc($gender) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="date_of_birth">Date of birth <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" id="date_of_birth" name="date_of_birth" value="<?= esc(old('date_of_birth')) ?>" required max="<?= esc(date('Y-m-d')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="guardian_name">Guardian name</label>
                        <input class="form-control" id="guardian_name" name="guardian_name" value="<?= esc(old('guardian_name')) ?>" maxlength="150">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="guardian_phone">Guardian phone</label>
                        <input class="form-control" id="guardian_phone" name="guardian_phone" value="<?= esc(old('guardian_phone')) ?>" maxlength="20">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" maxlength="5000"><?= esc(old('address', implode(', ', array_filter([$lead['city'], $lead['country']])))) ?></textarea>
                    </div>
                </div>
            </section>

            <section class="card card-soft p-3 p-lg-4">
                <h2 class="h5 fw-bold mb-1">Enrollment details</h2>
                <p class="text-muted fs-7 mb-4">Choose the active program and intake for the student's official record.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="department_id">Department <span class="text-danger">*</span></label>
                        <select class="form-select" id="department_id" name="department_id" required>
                            <option value="">Select department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['id'] ?>" <?= old('department_id') == $department['id'] ? 'selected' : '' ?>><?= esc($department['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="course_id">Course <span class="text-danger">*</span></label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">Select course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>" data-department="<?= $course['department_id'] ?>" data-semesters="<?= $course['total_semesters'] ?>" <?= old('course_id') == $course['id'] ? 'selected' : '' ?>><?= esc($course['code'] . ' - ' . $course['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($courses === []): ?><div class="form-text text-danger">Add an active course before enrolling a lead.</div><?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="semester">Starting semester <span class="text-danger">*</span></label>
                        <select class="form-select" id="semester" name="semester" required>
                            <option value="">Select semester</option>
                            <?php for ($semester = 1; $semester <= 12; $semester++): ?>
                                <option value="<?= $semester ?>" <?= old('semester') == $semester ? 'selected' : '' ?>>Semester <?= $semester ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="academic_year_id">Academic year <span class="text-danger">*</span></label>
                        <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                            <option value="">Select academic year</option>
                            <?php foreach ($academic_years as $year): ?>
                                <option value="<?= $year['id'] ?>" <?= old('academic_year_id', $year['is_current'] ? $year['id'] : '') == $year['id'] ? 'selected' : '' ?>><?= esc($year['year_label']) ?><?= $year['is_current'] ? ' (Current)' : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="admission_date">Admission date <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" id="admission_date" name="admission_date" value="<?= esc(old('admission_date', date('Y-m-d'))) ?>" required>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="card card-soft p-3 p-lg-4">
                <h2 class="h5 fw-bold mb-3">What happens next?</h2>
                <ul class="text-muted fs-7 ps-3 mb-4">
                    <li class="mb-2">Create a student portal account with a secure initial password.</li>
                    <li class="mb-2">Generate the official enrollment number.</li>
                    <li class="mb-2">Add the student to the enrolled-student directory.</li>
                    <li>Mark this lead as Won / Enrolled and record it in the timeline.</li>
                </ul>
                <button class="btn btn-success w-100 py-2" type="submit" <?= $courses === [] || $departments === [] || $academic_years === [] ? 'disabled' : '' ?>>
                    <i class="bi bi-mortarboard me-1"></i> Confirm enrollment
                </button>
                <a href="<?= base_url('leads/profile/' . $lead['id']) ?>" class="btn btn-light w-100 mt-2">Cancel</a>
            </section>
        </div>
    </div>
</form>

<script>
    const departmentSelect = document.getElementById('department_id');
    const courseSelect = document.getElementById('course_id');
    const semesterSelect = document.getElementById('semester');

    // Filters the course choices to the selected department.
    function updateAvailableCourses() {
        let currentCourseAvailable = false;
        Array.from(courseSelect.options).forEach((option, index) => {
            if (index === 0) return;
            const available = option.dataset.department === departmentSelect.value;
            option.hidden = !available;
            option.disabled = !available;
            if (available && option.selected) currentCourseAvailable = true;
        });
        if (!currentCourseAvailable) courseSelect.value = '';
        updateAvailableSemesters();
    } // End of updateAvailableCourses()

    // Limits semester choices to those offered by the selected course.
    function updateAvailableSemesters() {
        const selectedCourse = courseSelect.options[courseSelect.selectedIndex];
        const maxSemesters = Number(selectedCourse?.dataset.semesters || 0);
        Array.from(semesterSelect.options).forEach((option, index) => {
            if (index === 0) return;
            const available = Number(option.value) <= maxSemesters;
            option.hidden = !available;
            option.disabled = !available;
            if (!available && option.selected) semesterSelect.value = '';
        });
    } // End of updateAvailableSemesters()

    departmentSelect.addEventListener('change', updateAvailableCourses);
    courseSelect.addEventListener('change', updateAvailableSemesters);
    updateAvailableCourses();
</script>
<?= $this->endSection() ?>
