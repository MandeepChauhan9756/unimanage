<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('faculty') ?>" class="text-muted text-decoration-none fs-7"><i class="bi bi-arrow-left me-1"></i> Back to team</a>
    <div class="text-uppercase text-primary fw-bold fs-7 mt-3 mb-2">Academic setup</div>
    <h1 class="page-title mb-1">Assign a subject</h1>
    <p class="text-muted mb-0">Connect a faculty member to a subject for an academic year.</p>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger"><?php foreach (session()->getFlashdata('errors') as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if ($faculty_list === []): ?>
    <div class="alert alert-warning">No active faculty members are available. Add or reactivate a faculty member before assigning a subject.</div>
<?php endif; ?>

<div class="card card-soft p-3 p-lg-4" style="max-width:760px">
    <form action="<?= base_url('faculty/store-assignment') ?>" method="post">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold" for="faculty_id">Faculty member</label>
                <select class="form-select" id="faculty_id" name="faculty_id" required <?= $faculty_list === [] ? 'disabled' : '' ?>>
                    <option value="">Select a faculty member</option>
                    <?php foreach ($faculty_list as $member): ?>
                        <option value="<?= esc($member['id']) ?>" <?= old('faculty_id') == $member['id'] ? 'selected' : '' ?>><?= esc($member['full_name'] . ' · ' . $member['employee_id']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold" for="subject_id">Subject</label>
                <select class="form-select" id="subject_id" name="subject_id" required>
                    <option value="">Select a subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= esc($subject['id']) ?>" <?= old('subject_id') == $subject['id'] ? 'selected' : '' ?>><?= esc($subject['code'] . ' · ' . $subject['name'] . ' — ' . $subject['course_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold" for="academic_year_id">Academic year</label>
                <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                    <option value="">Select an academic year</option>
                    <?php foreach ($academic_years as $year): ?>
                        <option value="<?= esc($year['id']) ?>" <?= old('academic_year_id') == $year['id'] ? 'selected' : '' ?>><?= esc($year['year_label']) ?><?= $year['is_current'] ? ' · Current' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= base_url('faculty') ?>" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary" <?= $faculty_list === [] ? 'disabled' : '' ?>><i class="bi bi-check2 me-1"></i> Assign subject</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
