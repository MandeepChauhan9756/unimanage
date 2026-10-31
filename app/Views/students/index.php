<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Student Directory</h3>
        <p class="text-muted fs-7 mb-0">Manage and filter enrolled university students.</p>
    </div>
    <?php if (session()->get('role') === 'admin'): ?>
        <div>
            <?php $exportQuery = http_build_query(array_filter($filters, static /* Purpose: Removes empty filters from the student export query. */ fn ($value) => $value !== null && $value !== '' /* End of arrow callback */)); ?>
            <a href="<?= esc(base_url('students/export' . ($exportQuery ? '?' . $exportQuery : ''))) ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
            <a href="<?= base_url('students/create') ?>" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Add New Student
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4 p-3">
    <form method="get" action="<?= base_url('students') ?>" class="row g-3">
        <div class="col-md-4 col-lg-2">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone, enrollment no..." value="<?= esc($filters['search'] ?? '') ?>">
        </div>
        <div class="col-md-4 col-lg-2">
            <select name="department_id" class="form-select">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= $dept['id'] ?>" <?= ($filters['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>><?= esc($dept['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 col-lg-2">
            <select name="course_id" class="form-select">
                <option value="">All Courses</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= ($filters['course_id'] ?? '') == $course['id'] ? 'selected' : '' ?>><?= esc($course['code']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 col-lg-2">
            <select name="academic_year_id" class="form-select">
                <option value="">All Academic Years</option>
                <?php foreach ($academic_years as $year): ?>
                    <option value="<?= $year['id'] ?>" <?= ($filters['academic_year_id'] ?? '') == $year['id'] ? 'selected' : '' ?>><?= esc($year['year_label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 col-lg-1">
            <select name="semester" class="form-select">
                <option value="">All Semesters</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= ($filters['semester'] ?? '') == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4 col-lg-1">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" <?= ($filters['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="suspended" <?= ($filters['status'] ?? '') == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                <option value="graduated" <?= ($filters['status'] ?? '') == 'graduated' ? 'selected' : '' ?>>Graduated</option>
            </select>
        </div>
        <div class="col-md-2 col-lg-1 d-grid">
            <button type="submit" class="btn btn-dark"><i class="bi bi-search"></i></button>
        </div>
        <div class="col-md-2 col-lg-1 d-grid">
            <a href="<?= base_url('students') ?>" class="btn btn-light">Clear</a>
        </div>
    </form>
</div>

<!-- Students Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light fs-7 text-uppercase">
                <tr>
                    <th class="ps-3">Student</th>
                    <th>Enrollment No</th>
                    <th>Department</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Academic Year</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No student records match these filters.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2 bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden;">
                                        <?php if ($s['avatar'] && $s['avatar'] != 'default.png'): ?>
                                            <img src="<?= base_url('uploads/avatars/' . $s['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($s['full_name'], 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-dark d-block"><?= esc($s['full_name']) ?></span>
                                        <small class="text-muted"><?= esc($s['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><code><?= esc($s['enrollment_no']) ?></code></td>
                            <td><?= esc($s['department_name']) ?></td>
                            <td>
                                <span class="fw-semibold"><?= esc($s['course_code']) ?></span>
                                <small class="text-muted d-block"><?= esc($s['course_name']) ?></small>
                            </td>
                            <td>Sem <?= esc($s['semester']) ?></td>
                            <td><?= esc($s['year_label']) ?></td>
                            <td><?= esc($s['phone']) ?></td>
                            <td>
                                <?php
                                    $statusClasses = [
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'suspended' => 'danger',
                                        'graduated' => 'primary',
                                    ];
                                    $statusClass = $statusClasses[$s['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> border border-<?= $statusClass ?>-subtle px-2 py-1 rounded-pill"><?= esc(ucfirst($s['status'])) ?></span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="<?= base_url('students/profile/' . $s['id']) ?>" class="btn btn-sm btn-light text-primary" title="View Profile"><i class="bi bi-eye"></i></a>
                                <?php if (session()->get('role') === 'admin' && in_array($s['status'], ['active', 'inactive'], true)): ?>
                                    <form action="<?= base_url('students/toggle-status/' . $s['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-light text-warning" title="<?= $s['status'] === 'active' ? 'Deactivate student' : 'Activate student' ?>" onclick="return confirm('Are you sure you want to change this student status?');"><i class="bi bi-toggle-on"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($pager): ?>
        <div class="card-footer bg-white py-3 d-flex justify-content-end">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>