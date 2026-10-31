<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$isEdit = $lead !== null;
$value = static /* Purpose: Returns old form input or the lead’s current value. */ fn (string $field, mixed $fallback = '') => old($field, $lead[$field] ?? $fallback) /* End of arrow callback */;
$followUpValue = old('next_follow_up', $lead && $lead['next_follow_up'] ? date('Y-m-d\TH:i', strtotime($lead['next_follow_up'])) : '');
?>
<div class="mb-4">
    <a href="<?= $isEdit ? base_url('leads/profile/' . $lead['id']) : base_url('leads') ?>" class="text-muted text-decoration-none fs-7"><i class="bi bi-arrow-left me-1"></i> <?= $isEdit ? 'Back to student profile' : 'Back to leads' ?></a>
    <div class="d-flex justify-content-between align-items-end gap-3 mt-3">
        <div>
            <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Admissions pipeline</div>
            <h1 class="page-title mb-1"><?= esc($title) ?></h1>
            <p class="text-muted mb-0">Capture the student's goals and make the next conversation easy.</p>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Please review the highlighted information.</div>
        <ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form action="<?= $isEdit ? base_url('leads/update/' . $lead['id']) : base_url('leads/store') ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-xl-8">
            <section class="card card-soft p-3 p-lg-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="bg-primary-subtle text-primary rounded-3 p-2"><i class="bi bi-person-vcard fs-5"></i></span>
                    <div><h2 class="h5 fw-bold mb-1">Student details</h2><p class="text-muted fs-7 mb-0">How can your team reach and support this student?</p></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="student_name">Student name <span class="text-danger">*</span></label>
                        <input class="form-control" id="student_name" name="student_name" value="<?= esc($value('student_name')) ?>" required maxlength="150" autocomplete="name" placeholder="Full name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="phone">Phone number <span class="text-danger">*</span></label>
                        <input class="form-control" id="phone" name="phone" value="<?= esc($value('phone')) ?>" required maxlength="25" autocomplete="tel" placeholder="+91 98765 43210">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="email">Email address</label>
                        <input class="form-control" type="email" id="email" name="email" value="<?= esc($value('email')) ?>" maxlength="150" autocomplete="email" placeholder="student@email.com">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="city">City</label>
                        <input class="form-control" id="city" name="city" value="<?= esc($value('city')) ?>" maxlength="100" placeholder="e.g. Delhi">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="country">Country</label>
                        <input class="form-control" id="country" name="country" value="<?= esc($value('country')) ?>" maxlength="100" placeholder="e.g. India">
                    </div>
                </div>
            </section>

            <section class="card card-soft p-3 p-lg-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="bg-success-subtle text-success rounded-3 p-2"><i class="bi bi-mortarboard fs-5"></i></span>
                    <div><h2 class="h5 fw-bold mb-1">Study preferences</h2><p class="text-muted fs-7 mb-0">Record where and what the student wants to study.</p></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="study_level">Study level</label>
                        <select class="form-select" id="study_level" name="study_level">
                            <option value="">Choose a level</option>
                            <?php foreach ($studyLevels as $level): ?><option value="<?= esc($level) ?>" <?= $value('study_level') === $level ? 'selected' : '' ?>><?= esc($level) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="interested_program">Program / course of interest</label>
                        <input class="form-control" id="interested_program" name="interested_program" value="<?= esc($value('interested_program')) ?>" maxlength="150" placeholder="e.g. BSc Computer Science">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="preferred_university">Preferred university</label>
                        <input class="form-control" id="preferred_university" name="preferred_university" value="<?= esc($value('preferred_university')) ?>" maxlength="150" placeholder="University name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="intake">Target intake</label>
                        <input class="form-control" id="intake" name="intake" value="<?= esc($value('intake')) ?>" maxlength="30" placeholder="e.g. Fall 2027">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="notes">Counselor notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" maxlength="5000" placeholder="Student's goals, questions or context to remember..."><?= esc($value('notes')) ?></textarea>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="card card-soft p-3 p-lg-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="bg-warning-subtle text-warning-emphasis rounded-3 p-2"><i class="bi bi-kanban fs-5"></i></span>
                    <div><h2 class="h5 fw-bold mb-1">Lead management</h2><p class="text-muted fs-7 mb-0">Plan the next step for this inquiry.</p></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="status">Pipeline stage</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach ($statuses as $status): ?><option value="<?= esc($status) ?>" <?= $value('status', 'new') === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="lead_source">How did they find you?</label>
                    <select class="form-select" id="lead_source" name="lead_source" required>
                        <?php foreach ($sources as $source): ?><option value="<?= esc($source) ?>" <?= $value('lead_source', 'other') === $source ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $source))) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="assigned_to">Assigned counselor</label>
                    <select class="form-select" id="assigned_to" name="assigned_to">
                        <option value="">Unassigned</option>
                        <?php foreach ($counselors as $counselor): ?><option value="<?= esc($counselor['id']) ?>" <?= (string) $value('assigned_to') === (string) $counselor['id'] ? 'selected' : '' ?>><?= esc($counselor['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold" for="next_follow_up">Next follow-up</label>
                    <input class="form-control" type="datetime-local" id="next_follow_up" name="next_follow_up" value="<?= esc($followUpValue) ?>">
                    <div class="form-text">Set a reminder so no student is left waiting.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-check2 me-1"></i> <?= $isEdit ? 'Save changes' : 'Add student lead' ?></button>
                <a href="<?= $isEdit ? base_url('leads/profile/' . $lead['id']) : base_url('leads') ?>" class="btn btn-light w-100 mt-2">Cancel</a>
            </section>
            <div class="card border-0 bg-primary-subtle p-3">
                <div class="d-flex gap-2 text-primary"><i class="bi bi-shield-check mt-1"></i><div class="fs-7">Student contact details are available only to authorized admissions staff.</div></div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
