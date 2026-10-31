<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('fees') ?>" class="text-muted text-decoration-none fs-7"><i class="bi bi-arrow-left me-1"></i> Fees & payments</a>
    <h1 class="page-title mt-3 mb-1">Create fee invoice</h1>
    <p class="text-muted mb-0">Issue a charge to an enrolled student. Payments can be recorded against the invoice later.</p>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<section class="card card-soft p-3 p-lg-4" style="max-width:760px">
    <form action="<?= base_url('fees/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label fw-semibold" for="student_id">Student <span class="text-danger">*</span></label>
            <select class="form-select" name="student_id" id="student_id" required>
                <option value="">Select enrolled student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>" <?= old('student_id') == $student['id'] ? 'selected' : '' ?>>
                        <?= esc($student['full_name'] . ' · ' . $student['enrollment_no'] . ' · ' . $student['course_code']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($students === []): ?><div class="form-text text-danger">There are no active enrolled students to invoice.</div><?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold" for="title">Fee description <span class="text-danger">*</span></label>
            <input class="form-control" id="title" name="title" value="<?= esc(old('title')) ?>" maxlength="150" required placeholder="e.g. Semester 1 tuition fee">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold" for="description">Notes</label>
            <textarea class="form-control" id="description" name="description" rows="3" maxlength="2000" placeholder="Optional details"><?= esc(old('description')) ?></textarea>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="amount">Amount (INR) <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="amount" name="amount" value="<?= esc(old('amount')) ?>" min="0.01" max="9999999999.99" step="0.01" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="due_date">Due date <span class="text-danger">*</span></label>
                <input class="form-control" type="date" id="due_date" name="due_date" value="<?= esc(old('due_date', date('Y-m-d', strtotime('+14 days')))) ?>" required>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= base_url('fees') ?>" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary" <?= $students === [] ? 'disabled' : '' ?>><i class="bi bi-receipt me-1"></i> Create invoice</button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
