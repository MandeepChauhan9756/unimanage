<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('timetable') ?>" class="text-decoration-none text-muted fs-7 mb-2 d-inline-block">
        <i class="bi bi-arrow-left me-1"></i> Back to Timetable
    </a>
    <h3 class="fw-bold text-dark mb-0">Add Timetable Schedule Slot</h3>
</div>

<div class="card border-0 shadow-sm p-4">
    <form action="<?= base_url('timetable/store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Semester</label>
                <select name="semester" class="form-select" required>
                    <?php for($i=1; $i<=8; $i++): ?>
                        <option value="<?= $i ?>">Semester <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Subject</label>
                <select name="subject_id" class="form-select" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?> (<?= esc($s['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Faculty Instructor</label>
                <select name="faculty_id" class="form-select" required>
                    <option value="">Select Faculty</option>
                    <?php foreach ($faculty as $f): ?>
                        <option value="<?= $f['id'] ?>"><?= esc($f['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Academic Year</label>
                <select name="academic_year_id" class="form-select" required>
                    <?php foreach ($academic_years as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= $ay['is_current'] ? 'selected' : '' ?>><?= esc($ay['year_label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Classroom / Hall</label>
                <input type="text" name="classroom" class="form-control" placeholder="Room 302 / Lab A" required>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-medium">Day of Week</label>
                <select name="day_of_week" class="form-select" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Start Time</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">End Time</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Timetable Slot</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>