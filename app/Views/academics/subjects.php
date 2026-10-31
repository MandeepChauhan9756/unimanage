<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Academic setup</div>
        <h1 class="page-title mb-1">Subjects</h1>
        <p class="text-muted mb-0">Manage course subjects, semester placement and credits.</p>
    </div>
    <?php if (session()->get('role') === 'admin'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal"><i class="bi bi-plus-lg me-1"></i> Add subject</button>
    <?php endif; ?>
</div>

<section class="card card-soft">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-4">Subject</th><th>Course</th><th>Semester</th><th>Type</th><th>Credits</th><th>Status</th></tr></thead>
            <tbody>
            <?php if ($subjects === []): ?>
                <tr><td colspan="6" class="text-center text-muted py-5">No subjects have been added yet.</td></tr>
            <?php else: foreach ($subjects as $subject): ?>
                <tr>
                    <td class="ps-4"><div class="fw-semibold"><?= esc($subject['name']) ?></div><code class="fs-7"><?= esc($subject['code']) ?></code></td>
                    <td><?= esc($subject['course_name']) ?><div class="text-muted fs-7"><?= esc($subject['course_code']) ?></div></td>
                    <td>Semester <?= esc($subject['semester']) ?></td>
                    <td><?= esc($subject['type']) ?></td>
                    <td><?= esc($subject['credits']) ?></td>
                    <td><span class="status-pill <?= $subject['status'] === 'active' ? 'status-enrolled' : 'status-lost' ?>"><?= esc(ucfirst($subject['status'])) ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if (session()->get('role') === 'admin'): ?>
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="<?= base_url('academics/subjects/store') ?>" method="post" class="modal-content">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h2 class="modal-title h5 fw-bold" id="addSubjectModalLabel">Add a subject</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger fs-7"><?php foreach (session()->getFlashdata('errors') as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="course_id">Course</label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">Select a course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= esc($course['id']) ?>" <?= old('course_id') == $course['id'] ? 'selected' : '' ?>><?= esc($course['code'] . ' — ' . $course['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="code">Subject code</label>
                            <input class="form-control" id="code" name="code" value="<?= esc(old('code')) ?>" maxlength="20" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="semester">Semester</label>
                            <input class="form-control" type="number" id="semester" name="semester" value="<?= esc(old('semester', '1')) ?>" min="1" max="12" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="name">Subject name</label>
                            <input class="form-control" id="name" name="name" value="<?= esc(old('name')) ?>" maxlength="150" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" for="type">Subject type</label>
                            <select class="form-select" id="type" name="type" required>
                                <?php foreach (['Theory', 'Practical', 'Elective'] as $type): ?><option value="<?= esc($type) ?>" <?= old('type', 'Theory') === $type ? 'selected' : '' ?>><?= esc($type) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="credits">Credits</label>
                            <input class="form-control" type="number" id="credits" name="credits" value="<?= esc(old('credits', '3')) ?>" min="1" max="99" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save subject</button></div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
