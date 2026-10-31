<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('leads') ?>" class="text-muted text-decoration-none fs-7"><i class="bi bi-arrow-left me-1"></i> All admissions leads</a>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
        <div class="d-flex align-items-center gap-3">
            <span class="avatar-sm fs-5" style="height:54px;width:54px"><?= esc(strtoupper(substr($lead['student_name'], 0, 1))) ?></span>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="page-title mb-0"><?= esc($lead['student_name']) ?></h1>
                    <span class="status-pill status-<?= esc($lead['status']) ?>"><?= $lead['status'] === 'enrolled' ? 'Won / Enrolled' : esc(ucfirst($lead['status'])) ?></span>
                </div>
                <div class="text-muted mt-1"><?= esc($lead['interested_program'] ?: 'Program not selected') ?><?= $lead['study_level'] ? ' · ' . esc($lead['study_level']) : '' ?></div>
            </div>
        </div>
        <?php if ($lead['status'] !== 'enrolled'): ?>
            <a href="<?= base_url('leads/edit/' . $lead['id']) ?>" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i> Edit lead</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($initialPassword): ?>
    <div class="alert alert-success">
        <div class="fw-semibold"><i class="bi bi-check-circle me-1"></i> Student account created</div>
        <div class="fs-7 mt-1">Share this initial password securely with the student. It is shown only once: <code><?= esc($initialPassword) ?></code></div>
    </div>
<?php endif; ?>

<section class="card card-soft p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <?php
            $progressStages = ['new' => 'New', 'contacted' => 'Contacted', 'counseling' => 'Counseling', 'application' => 'Application', 'enrolled' => 'Won'];
            $stageOrder = array_keys($progressStages);
            $currentStageIndex = array_search($lead['status'], $stageOrder, true);
        ?>
        <?php foreach ($progressStages as $stage => $label): ?>
            <?php $isComplete = $lead['status'] !== 'lost' && $currentStageIndex !== false && array_search($stage, $stageOrder, true) <= $currentStageIndex; ?>
                <div class="d-flex align-items-center gap-2 <?= $isComplete ? 'text-success fw-semibold' : 'text-muted' ?>">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center <?= $isComplete ? 'bg-success-subtle text-success' : 'bg-light text-muted' ?>" style="width:28px;height:28px">
                        <i class="bi <?= $isComplete ? 'bi-check-lg' : 'bi-circle' ?>"></i>
                    </span>
                    <span class="fs-7"><?= esc($label) ?></span>
                </div>
        <?php endforeach; ?>
        <?php if ($lead['status'] === 'lost'): ?><span class="status-pill status-lost">Closed / not proceeding</span><?php endif; ?>
    </div>
</section>

<?php if ($lead['student_id']): ?>
    <section class="card card-soft p-3 p-lg-4 mb-4 border-success">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="fw-bold text-success"><i class="bi bi-mortarboard me-1"></i> Lead won — student enrolled</div>
                <div class="text-muted fs-7 mt-1">Enrollment No: <code><?= esc($lead['enrollment_no']) ?></code><?= $lead['converted_at'] ? ' · ' . esc(date('M j, Y', strtotime($lead['converted_at']))) : '' ?></div>
            </div>
            <a href="<?= base_url('students/profile/' . $lead['student_id']) ?>" class="btn btn-success"><i class="bi bi-person-vcard me-1"></i> Open student profile</a>
        </div>
    </section>
<?php elseif ($lead['status'] === 'application'): ?>
    <section class="card card-soft p-3 p-lg-4 mb-4 border-success">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="fw-bold">Application is ready to close</div>
                <div class="text-muted fs-7 mt-1">Complete the enrollment details to mark this lead as Won and add the student to university records.</div>
            </div>
            <a href="<?= base_url('leads/convert/' . $lead['id']) ?>" class="btn btn-success"><i class="bi bi-mortarboard me-1"></i> Mark Won & Enroll</a>
        </div>
    </section>
<?php endif; ?>

<div class="row g-4">
    <div class="col-xl-8">
        <section class="card card-soft p-3 p-lg-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div><h2 class="h5 fw-bold mb-1">Student overview</h2><p class="text-muted fs-7 mb-0">Admissions details and counselor ownership.</p></div>
                <span class="bg-primary-subtle text-primary rounded-3 p-2"><i class="bi bi-person-lines-fill fs-5"></i></span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted fs-7 mb-1">Phone number</div>
                    <a class="fw-semibold text-dark text-decoration-none" href="tel:<?= esc($lead['phone']) ?>"><?= esc($lead['phone']) ?></a>
                </div>
                <div class="col-md-6">
                    <div class="text-muted fs-7 mb-1">Email address</div>
                    <?php if ($lead['email']): ?><a class="fw-semibold text-dark text-decoration-none" href="mailto:<?= esc($lead['email']) ?>"><?= esc($lead['email']) ?></a><?php else: ?><span class="text-muted">Not provided</span><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="text-muted fs-7 mb-1">Location</div>
                    <div class="fw-semibold"><?= esc(implode(', ', array_filter([$lead['city'], $lead['country']])) ?: 'Not provided') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted fs-7 mb-1">Preferred university</div>
                    <div class="fw-semibold"><?= esc($lead['preferred_university'] ?: 'Not selected') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted fs-7 mb-1">Target intake</div>
                    <div class="fw-semibold"><?= esc($lead['intake'] ?: 'Not selected') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted fs-7 mb-1">Lead source</div>
                    <div class="fw-semibold"><?= esc(ucwords(str_replace('_', ' ', $lead['lead_source']))) ?></div>
                </div>
            </div>
            <?php if ($lead['notes']): ?>
                <div class="border-top mt-4 pt-3">
                    <div class="text-muted fs-7 mb-1">Counselor notes</div>
                    <div class="text-break"><?= nl2br(esc($lead['notes'])) ?></div>
                </div>
            <?php endif; ?>
        </section>

        <section class="card card-soft p-3 p-lg-4">
            <div class="mb-4"><h2 class="h5 fw-bold mb-1">Conversation timeline</h2><p class="text-muted fs-7 mb-0">Calls, messages and counseling updates in one place.</p></div>
            <?php if ($activities === []): ?>
                <div class="text-muted fs-7 mb-4">No activity recorded yet. Add the first update after your next conversation.</div>
            <?php else: ?>
                <div class="position-relative ms-2 mb-4" style="border-left:1px solid #e6eaf2">
                    <?php foreach ($activities as $activity): ?>
                        <div class="position-relative ps-4 pb-4">
                            <span class="position-absolute bg-primary rounded-circle" style="height:9px;width:9px;left:-5px;top:5px"></span>
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-1">
                                <span class="fw-semibold"><?= esc($activity['user_name'] ?: 'System') ?> <span class="text-muted fw-normal">· <?= esc(ucfirst($activity['activity_type'])) ?></span></span>
                                <span class="text-muted fs-7"><?= esc(date('M j, Y · g:i A', strtotime($activity['created_at']))) ?></span>
                            </div>
                            <div class="text-muted fs-7 text-break"><?= nl2br(esc($activity['details'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('leads/activity/' . $lead['id']) ?>" method="post" class="border-top pt-4">
                <?= csrf_field() ?>
                <label class="form-label fw-semibold" for="activity_type">Add an update</label>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <select class="form-select" id="activity_type" name="activity_type" required>
                            <?php foreach ($activityTypes as $type): ?><option value="<?= esc($type) ?>" <?= old('activity_type') === $type ? 'selected' : '' ?>><?= esc(ucfirst($type)) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8"><textarea class="form-control" name="details" rows="2" required minlength="3" maxlength="2000" placeholder="What happened? Add a helpful note for the team..."><?= esc(old('details')) ?></textarea></div>
                </div>
                <?php if (session()->getFlashdata('errors')): ?><div class="text-danger fs-7 mb-2"><?php foreach (session()->getFlashdata('errors') as $error): ?><?= esc($error) ?> <?php endforeach; ?></div><?php endif; ?>
                <div class="d-flex justify-content-end"><button class="btn btn-primary btn-sm px-3" type="submit"><i class="bi bi-plus-lg me-1"></i> Add to timeline</button></div>
            </form>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="card card-soft p-3 p-lg-4 mb-4">
            <h2 class="h5 fw-bold mb-4">Next steps</h2>
            <div class="d-flex gap-3 mb-4">
                <span class="bg-warning-subtle text-warning-emphasis rounded-3 p-2"><i class="bi bi-calendar-event"></i></span>
                <div><div class="text-muted fs-7 mb-1">Next follow-up</div>
                    <?php if ($lead['next_follow_up']): ?>
                        <div class="fw-semibold"><?= esc(date('M j, Y', strtotime($lead['next_follow_up']))) ?></div>
                        <div class="text-muted fs-7"><?= esc(date('g:i A', strtotime($lead['next_follow_up']))) ?></div>
                    <?php else: ?><div class="fw-semibold">Not scheduled</div><?php endif; ?>
                </div>
            </div>
            <div class="d-flex gap-3 mb-4">
                <span class="bg-primary-subtle text-primary rounded-3 p-2"><i class="bi bi-person-check"></i></span>
                <div><div class="text-muted fs-7 mb-1">Assigned counselor</div><div class="fw-semibold"><?= esc($lead['counselor_name'] ?: 'Unassigned') ?></div></div>
            </div>
            <div class="d-flex gap-3 mb-4">
                <span class="bg-success-subtle text-success rounded-3 p-2"><i class="bi bi-calendar-plus"></i></span>
                <div><div class="text-muted fs-7 mb-1">Lead received</div><div class="fw-semibold"><?= esc(date('M j, Y', strtotime($lead['created_at']))) ?></div></div>
            </div>
            <a href="<?= base_url('leads/edit/' . $lead['id']) ?>" class="btn btn-outline-primary w-100"><i class="bi bi-pencil me-1"></i> Update stage or follow-up</a>
        </section>
        <div class="card border-0 bg-primary-subtle p-3">
            <div class="d-flex gap-2 text-primary"><i class="bi bi-lightbulb mt-1"></i><div class="fs-7">A timely, personal follow-up can make a student's university decision much easier.</div></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
