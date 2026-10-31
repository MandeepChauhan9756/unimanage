<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <a href="<?= base_url('attendance') ?>" class="text-decoration-none text-muted fs-7"><i class="bi bi-arrow-left me-1"></i> Attendance logs</a>
        <h1 class="page-title mt-3 mb-1">Mark one student</h1>
        <p class="text-muted mb-0">Record or correct attendance for an individual student and class session.</p>
    </div>
    <a href="<?= base_url('attendance/mark') ?>" class="btn btn-outline-secondary"><i class="bi bi-people me-1"></i> Mark whole class</a>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<section class="card card-soft p-3 p-lg-4 mb-4">
    <h2 class="h5 fw-bold mb-1">Select class and student</h2>
    <p class="text-muted fs-7 mb-4">The student list is limited to the selected subject, course, semester and academic year.</p>
    <form method="get" action="<?= base_url('attendance/mark-student') ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="subject_id">Subject</label>
            <select class="form-select" name="subject_id" id="subject_id" required>
                <option value="">Select subject</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id'] ?>" <?= $selected_subject_id === (int) $subject['id'] ? 'selected' : '' ?>><?= esc($subject['code'] . ' · ' . $subject['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" for="academic_year_id">Academic year</label>
            <select class="form-select" name="academic_year_id" id="academic_year_id" required>
                <option value="">Select academic year</option>
                <?php foreach ($academic_years as $year): ?>
                    <option value="<?= $year['id'] ?>" <?= $selected_year_id === (int) $year['id'] || (!$selected_year_id && $year['is_current']) ? 'selected' : '' ?>><?= esc($year['year_label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (session()->get('role') === 'admin' && $selected_subject_id && $selected_year_id): ?>
            <div class="col-12">
                <label class="form-label fw-semibold" for="faculty_id">Assigned faculty <span class="text-danger">*</span></label>
                <select class="form-select" name="faculty_id" id="faculty_id" <?= $faculty_options !== [] ? 'required' : '' ?>>
                    <option value="">Select assigned faculty</option>
                    <?php foreach ($faculty_options as $faculty): ?>
                        <option value="<?= $faculty['id'] ?>" <?= $selected_faculty_id === (int) $faculty['id'] ? 'selected' : '' ?>><?= esc($faculty['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($faculty_options === []): ?>
                    <div class="form-text text-danger">Assign an active faculty member to this subject and academic year first. <a href="<?= base_url('faculty/assign-subject') ?>">Assign a subject</a>.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="col-md-3">
            <label class="form-label fw-semibold" for="date">Class date</label>
            <input class="form-control" type="date" name="date" id="date" value="<?= esc($selected_date) ?>" max="<?= esc(date('Y-m-d')) ?>" required>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-outline-primary" type="submit">Load students</button></div>
        <?php if ($selected_subject_id && $selected_year_id): ?>
            <div class="col-md-9">
                <label class="form-label fw-semibold" for="student_id">Student</label>
                <select class="form-select" name="student_id" id="student_id" required>
                    <option value="">Select student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['id'] ?>" <?= $selected_student_id === (int) $student['id'] ? 'selected' : '' ?>><?= esc($student['full_name'] . ' · ' . $student['enrollment_no']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($students === []): ?><div class="form-text text-danger">No active students are enrolled in the selected class/year.</div><?php endif; ?>
            </div>
            <div class="col-md-3 d-grid"><button class="btn btn-outline-primary" type="submit">Load attendance</button></div>
        <?php endif; ?>
    </form>
</section>

<?php if ($selected_subject_id && $selected_year_id && $selected_student_id && $students !== []): ?>
    <?php $selectedStudent = null; foreach ($students as $student) { if ((int) $student['id'] === $selected_student_id) { $selectedStudent = $student; break; } } ?>
    <?php if ($selectedStudent): ?>
        <section class="card card-soft p-3 p-lg-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="avatar-sm"><?= esc(strtoupper(substr($selectedStudent['full_name'], 0, 1))) ?></span>
                <div>
                    <h2 class="h5 fw-bold mb-1"><?= esc($selectedStudent['full_name']) ?></h2>
                    <div class="text-muted fs-7"><code><?= esc($selectedStudent['enrollment_no']) ?></code> · <?= esc($selectedStudent['course_name']) ?> · Semester <?= esc($selectedStudent['semester']) ?></div>
                </div>
            </div>
            <form action="<?= base_url('attendance/store-student') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="student_id" value="<?= esc($selected_student_id) ?>">
                <input type="hidden" name="subject_id" value="<?= esc($selected_subject_id) ?>">
                <input type="hidden" name="academic_year_id" value="<?= esc($selected_year_id) ?>">
                <input type="hidden" name="date" value="<?= esc($selected_date) ?>">
                <?php if (session()->get('role') === 'admin'): ?>
                    <input type="hidden" name="faculty_id" value="<?= esc($selected_faculty_id) ?>">
                <?php endif; ?>
                <label class="form-label fw-semibold d-block mb-2">Attendance status for <?= esc($selected_date) ?></label>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php foreach (['Present' => 'success', 'Absent' => 'danger', 'Late' => 'warning', 'Excused' => 'secondary'] as $status => $color): ?>
                        <?php $inputId = 'individual_' . strtolower($status); ?>
                        <input type="radio" class="btn-check" name="status" value="<?= esc($status) ?>" id="<?= esc($inputId) ?>" <?= old('status', $existing_status ?? 'Present') === $status ? 'checked' : '' ?> required>
                        <label class="btn btn-outline-<?= esc($color) ?> px-4" for="<?= esc($inputId) ?>"><?= esc($status) ?></label>
                    <?php endforeach; ?>
                </div>
                <?php if ($existing_status): ?><div class="text-muted fs-7 mb-3">An attendance entry exists for this session; saving will update it.</div><?php endif; ?>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary px-4" type="submit" <?= session()->get('role') === 'admin' && ($faculty_options === [] || ! $selected_faculty_id) ? 'disabled' : '' ?>><i class="bi bi-check2-circle me-1"></i> Save student attendance</button>
                </div>
            </form>
        </section>
    <?php endif; ?>
<?php endif; ?>
<?= $this->endSection() ?>
