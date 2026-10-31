<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('students') ?>" class="text-decoration-none text-muted fs-7 mb-2 d-inline-block">
        <i class="bi bi-arrow-left me-1"></i> Back to Student Directory
    </a>
    <h3 class="fw-bold text-dark mb-0">Add New Student Admission</h3>
    <p class="text-muted fs-7 mb-0">Register a new student and provision their portal access account.</p>
</div>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger shadow-sm">
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm p-4">
    <form action="<?= base_url('students/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <h5 class="fw-semibold text-primary mb-3"><i class="bi bi-person me-2"></i>Personal Information</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-medium">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Email Address (Login ID)</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= old('gender') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="<?= old('date_of_birth') ?>" required>
            </div>
        </div>

        <h5 class="fw-semibold text-primary mb-3"><i class="bi bi-mortarboard me-2"></i>Academic & Enrollment Details</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-medium">Department</label>
                <select name="department_id" class="form-select" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= old('department_id') == $dept['id'] ? 'selected' : '' ?>><?= esc($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Course</label>
                <select name="course_id" id="courseId" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['id'] ?>" data-department="<?= $course['department_id'] ?>" data-semesters="<?= $course['total_semesters'] ?>" <?= old('course_id') == $course['id'] ? 'selected' : '' ?>>
                            <?= esc($course['code'] . ' - ' . $course['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($courses)): ?>
                    <small class="text-danger">Add an active course before enrolling a student.</small>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Semester</label>
                <select name="semester" class="form-select" required>
                    <option value="">Select Semester</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= old('semester') == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Academic Year</label>
                <select name="academic_year_id" class="form-select" required>
                    <option value="">Select Academic Year</option>
                    <?php foreach ($academic_years as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= old('academic_year_id', $ay['is_current'] ? $ay['id'] : '') == $ay['id'] ? 'selected' : '' ?>><?= esc($ay['year_label']) ?><?= $ay['is_current'] ? ' (Current)' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Admission Date</label>
                <input type="date" name="admission_date" class="form-control" value="<?= old('admission_date', date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Profile Photograph</label>
                <input type="file" name="avatar" class="form-control" accept="image/png, image/jpeg">
                <small class="text-muted">Optional; PNG or JPEG, up to 2 MB.</small>
            </div>
        </div>

        <h5 class="fw-semibold text-primary mb-3"><i class="bi bi-shield-check me-2"></i>Guardian & Contact Information</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-medium">Guardian Name</label>
                <input type="text" name="guardian_name" class="form-control" value="<?= old('guardian_name') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Guardian Phone</label>
                <input type="text" name="guardian_phone" class="form-control" value="<?= old('guardian_phone') ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-medium">Residential Address</label>
                <textarea name="address" class="form-control" rows="2"><?= old('address') ?></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('students') ?>" class="btn btn-light px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">Enroll Student</button>
        </div>
    </form>
</div>
<script>
    const departmentSelect = document.querySelector('[name="department_id"]');
    const courseSelect = document.getElementById('courseId');
    const semesterSelect = document.querySelector('[name="semester"]');

    // Filters the course choices to the selected department.
    function updateCourses() {
        const departmentId = departmentSelect.value;
        let selectedCourseAvailable = false;

        Array.from(courseSelect.options).forEach((option, index) => {
            if (index === 0) return;
            const available = option.dataset.department === departmentId;
            option.hidden = !available;
            option.disabled = !available;
            if (available && option.selected) selectedCourseAvailable = true;
        });

        if (!selectedCourseAvailable) courseSelect.value = '';
        updateSemesters();
    } // End of updateCourses()

    // Limits semester choices to those offered by the selected course.
    function updateSemesters() {
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        const maxSemesters = Number(selectedOption?.dataset.semesters || 0);

        Array.from(semesterSelect.options).forEach((option, index) => {
            if (index === 0) return;
            const available = Number(option.value) <= maxSemesters;
            option.hidden = !available;
            option.disabled = !available;
            if (!available && option.selected) semesterSelect.value = '';
        });
    } // End of updateSemesters()

    departmentSelect.addEventListener('change', updateCourses);
    courseSelect.addEventListener('change', updateSemesters);
    updateCourses();
</script>
<?= $this->endSection() ?>