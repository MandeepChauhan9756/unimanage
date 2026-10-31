<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Course Management</h3>
        <p class="text-muted fs-7 mb-0">Manage degree programs and courses.</p>
    </div>
    <?php if (session()->get('role') === 'admin'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="bi bi-plus-lg me-1"></i> Add Course
        </button>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light fs-7 text-uppercase">
                <tr>
                    <th class="ps-3">Code</th>
                    <th>Course Name</th>
                    <th>Department</th>
                    <th>Semesters</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $c): ?>
                    <tr>
                        <td class="ps-3"><code><?= esc($c['code']) ?></code></td>
                        <td class="fw-semibold"><?= esc($c['name']) ?></td>
                        <td><?= esc($c['department_name']) ?></td>
                        <td><?= esc($c['total_semesters']) ?> Semesters</td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Active</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('academics/courses/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Department</label>
                        <select name="department_id" class="form-select" required>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= esc($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Course Code</label>
                        <input type="text" name="code" class="form-control" placeholder="BTECH-CS" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Course Title</label>
                        <input type="text" name="name" class="form-control" placeholder="Bachelor of Technology in CS" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Total Semesters</label>
                        <input type="number" name="total_semesters" class="form-control" value="8" min="1" max="12" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Save Course</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>