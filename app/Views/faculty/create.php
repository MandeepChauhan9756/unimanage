<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('faculty') ?>" class="text-decoration-none text-muted fs-7 mb-2 d-inline-block">
        <i class="bi bi-arrow-left me-1"></i> Back to Faculty List
    </a>
    <h3 class="fw-bold text-dark mb-0">Register New Faculty Member</h3>
    <p class="text-muted fs-7 mb-0">Add a professor or lecturer to a university department.</p>
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
    <form action="<?= base_url('faculty/store') ?>" method="post">
        <?= csrf_field() ?>

        <h5 class="fw-semibold text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Personal & Account Information</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-medium">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Dr. Jane Doe" value="<?= old('name') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Official Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="jane.doe@unimanage.edu" value="<?= old('email') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Department</label>
                <select name="department_id" class="form-select" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= old('department_id') == $dept['id'] ? 'selected' : '' ?>><?= esc($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h5 class="fw-semibold text-primary mb-3"><i class="bi bi-mortarboard me-2"></i>Academic Profile</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-medium">Designation</label>
                <input type="text" name="designation" class="form-control" placeholder="Associate Professor" value="<?= old('designation') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Highest Qualification</label>
                <input type="text" name="qualification" class="form-control" placeholder="Ph.D. in Computer Science" value="<?= old('qualification') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Joining Date</label>
                <input type="date" name="joining_date" class="form-control" value="<?= old('joining_date', date('Y-m-d')) ?>" required>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('faculty') ?>" class="btn btn-light px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">Register Faculty Member</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>