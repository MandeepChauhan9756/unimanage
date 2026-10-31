<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <a href="<?= base_url('attendance') ?>" class="text-decoration-none text-muted fs-7"><i class="bi bi-arrow-left me-1"></i> Attendance logs</a>
        <h1 class="page-title mt-3 mb-1">Mark class attendance</h1>
        <p class="text-muted mb-0">Choose a class to load its active student roster. Existing marks are updated, not replaced.</p>
    </div>
    <a href="<?= base_url('attendance/mark-student') ?>" class="btn btn-outline-primary"><i class="bi bi-person-check me-1"></i> Mark one student</a>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<section class="card card-soft p-3 p-lg-4 mb-4">
    <form method="get" action="<?= base_url('attendance/mark') ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" class="form-select" required>
                <option value="">Select subject</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id'] ?>" <?= $selected_subject_id === (int) $subject['id'] ? 'selected' : '' ?>><?= esc($subject['code'] . ' · ' . $subject['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" for="academic_year_id">Academic year</label>
            <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                <option value="">Select academic year</option>
                <?php foreach ($academic_years as $year): ?>
                    <option value="<?= $year['id'] ?>" <?= $selected_year_id === (int) $year['id'] || (!$selected_year_id && $year['is_current']) ? 'selected' : '' ?>><?= esc($year['year_label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" for="date">Class date</label>
            <input type="date" name="date" id="date" class="form-control" value="<?= esc($selected_date) ?>" max="<?= esc(date('Y-m-d')) ?>" required>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary" type="submit">Load roster</button></div>
    </form>
</section>

<?php if ($selected_subject_id && $selected_year_id): ?>
    <?php if ($students === []): ?>
        <section class="card card-soft p-4 text-center">
            <i class="bi bi-people fs-2 text-muted mb-2"></i>
            <div class="fw-semibold">No active students are enrolled in this class and academic year.</div>
        </section>
    <?php else: ?>
        <?php $oldStatuses = old('status') ?? []; ?>
        <form action="<?= base_url('attendance/store') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="subject_id" value="<?= esc($selected_subject_id) ?>">
            <input type="hidden" name="academic_year_id" value="<?= esc($selected_year_id) ?>">
            <input type="hidden" name="date" value="<?= esc($selected_date) ?>">
            <?php if (session()->get('role') === 'admin'): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="faculty_id">Assigned faculty <span class="text-danger">*</span></label>
                    <select class="form-select" name="faculty_id" id="faculty_id" required>
                        <option value="">Select assigned faculty</option>
                        <?php foreach ($faculty_options as $faculty): ?>
                            <option value="<?= $faculty['id'] ?>" <?= old('faculty_id') == $faculty['id'] ? 'selected' : '' ?>><?= esc($faculty['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($faculty_options === []): ?><div class="form-text text-danger">Assign an active faculty member to this subject and academic year first.</div><?php endif; ?>
                </div>
            <?php endif; ?>
            <section class="card card-soft">
                <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div><h2 class="h5 fw-bold mb-1">Student roster</h2><p class="text-muted fs-7 mb-0"><?= count($students) ?> active students · <?= esc($selected_date) ?></p></div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Set all attendance">
                        <button class="btn btn-outline-success" type="button" data-set-all="Present">Set all present</button>
                        <button class="btn btn-outline-danger" type="button" data-set-all="Absent">Set all absent</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th class="ps-4">Student</th><th>Enrollment number</th><th class="text-center pe-4">Attendance status</th></tr></thead>
                        <tbody>
                        <?php foreach ($students as $student): ?>
                            <?php $savedStatus = $oldStatuses[$student['id']] ?? $existing_statuses[$student['id']] ?? 'Present'; ?>
                            <tr>
                                <td class="ps-4 fw-semibold"><?= esc($student['full_name']) ?></td>
                                <td><code><?= esc($student['enrollment_no']) ?></code></td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group" aria-label="Attendance status for <?= esc($student['full_name']) ?>">
                                        <?php foreach (['Present' => 'success', 'Absent' => 'danger', 'Late' => 'warning', 'Excused' => 'secondary'] as $status => $color): ?>
                                            <?php $inputId = 'status_' . $student['id'] . '_' . strtolower($status); ?>
                                            <input type="radio" class="btn-check attendance-status" name="status[<?= $student['id'] ?>]" value="<?= esc($status) ?>" id="<?= esc($inputId) ?>" <?= $savedStatus === $status ? 'checked' : '' ?> required>
                                            <label class="btn btn-outline-<?= esc($color) ?> btn-sm" for="<?= esc($inputId) ?>"><?= esc($status) ?></label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top d-flex justify-content-end">
                    <button class="btn btn-primary px-4" type="submit"><i class="bi bi-check2-circle me-1"></i> Save attendance</button>
                </div>
            </section>
        </form>
        <script>
            document.querySelectorAll('[data-set-all]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('.attendance-status[value="' + button.dataset.setAll + '"]').forEach((input) => {
                        input.checked = true;
                    });
                });
            });
        </script>
    <?php endif; ?>
<?php endif; ?>
<?= $this->endSection() ?>
