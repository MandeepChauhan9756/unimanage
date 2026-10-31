<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    @media print {
        .sidebar, .app-header, .footer, .btn, form { display: none !important; }
        .main-content { padding: 0 !important; }
        .card { break-inside: avoid; box-shadow: none !important; }
    }
</style>
<div class="mb-4">
    <a href="<?= base_url(session()->get('role') === 'student' ? 'fees/my-fees' : 'fees') ?>" class="text-muted text-decoration-none fs-7"><i class="bi bi-arrow-left me-1"></i> <?= session()->get('role') === 'student' ? 'My fees' : 'Fees & payments' ?></a>
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mt-3">
        <div>
            <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Fee invoice</div>
            <h1 class="page-title mb-1"><?= esc($invoice['invoice_no']) ?></h1>
            <p class="text-muted mb-0"><?= esc($invoice['title']) ?></p>
        </div>
        <?php
            $statusColors = ['paid' => 'success', 'partial' => 'info', 'overdue' => 'danger', 'unpaid' => 'warning'];
            $statusColor = $statusColors[$invoice['fee_status']] ?? 'secondary';
        ?>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
            <span class="badge bg-<?= esc($statusColor) ?>-subtle text-<?= esc($statusColor) ?> border border-<?= esc($statusColor) ?>-subtle rounded-pill px-3 py-2"><?= esc(ucfirst($invoice['fee_status'])) ?></span>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-xl-7">
        <section class="card card-soft p-3 p-lg-4 mb-4">
            <h2 class="h5 fw-bold mb-3">Invoice details</h2>
            <div class="row g-3">
                <div class="col-md-6"><div class="text-muted fs-7 mb-1">Student</div><div class="fw-semibold"><?= esc($invoice['student_name']) ?></div><div class="text-muted fs-7"><?= esc($invoice['enrollment_no']) ?></div></div>
                <div class="col-md-6"><div class="text-muted fs-7 mb-1">Department</div><div class="fw-semibold"><?= esc($invoice['department_name']) ?></div></div>
                <div class="col-md-6"><div class="text-muted fs-7 mb-1">Due date</div><div class="fw-semibold"><?= esc(date('M j, Y', strtotime($invoice['due_date']))) ?></div></div>
                <div class="col-md-6"><div class="text-muted fs-7 mb-1">Academic year</div><div class="fw-semibold"><?= esc($invoice['year_label'] ?? 'Not specified') ?></div></div>
            </div>
            <?php if ($invoice['description']): ?><div class="border-top mt-3 pt-3"><div class="text-muted fs-7 mb-1">Notes</div><div><?= nl2br(esc($invoice['description'])) ?></div></div><?php endif; ?>
            <hr>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Invoice amount</span><strong>₹<?= esc(number_format((float) $invoice['amount'], 2)) ?></strong></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Amount paid</span><strong class="text-success">₹<?= esc(number_format((float) $invoice['paid_amount'], 2)) ?></strong></div>
            <div class="d-flex justify-content-between fs-5"><span class="fw-semibold">Balance due</span><strong>₹<?= esc(number_format((float) $invoice['balance_amount'], 2)) ?></strong></div>
        </section>

        <section class="card card-soft p-3 p-lg-4">
            <h2 class="h5 fw-bold mb-3">Payment history</h2>
            <?php if ($payments === []): ?>
                <p class="text-muted mb-0">No payments have been recorded for this invoice.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th>Received by</th><th class="text-end">Amount</th></tr></thead>
                        <tbody><?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= esc(date('M j, Y', strtotime($payment['payment_date']))) ?></td>
                                <td><?= esc(ucwords(str_replace('_', ' ', $payment['payment_method']))) ?></td>
                                <td><?= esc($payment['reference_no'] ?: '—') ?></td>
                                <td><?= esc($payment['received_by_name'] ?: 'Staff') ?></td>
                                <td class="text-end fw-semibold text-success">₹<?= esc(number_format((float) $payment['amount'], 2)) ?></td>
                            </tr>
                        <?php endforeach; ?></tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <?php if (in_array(session()->get('role'), ['admin', 'accountant'], true) && (float) $invoice['balance_amount'] > 0): ?>
        <div class="col-xl-5">
            <section class="card card-soft p-3 p-lg-4">
                <h2 class="h5 fw-bold mb-1">Record a payment</h2>
                <p class="text-muted fs-7 mb-4">Maximum remaining balance: ₹<?= esc(number_format((float) $invoice['balance_amount'], 2)) ?></p>
                <form action="<?= base_url('fees/invoice/' . $invoice['id'] . '/payment') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="amount">Amount received (INR) <span class="text-danger">*</span></label>
                        <input class="form-control" type="number" id="amount" name="amount" min="0.01" max="<?= esc(number_format((float) $invoice['balance_amount'], 2, '.', '')) ?>" step="0.01" value="<?= esc(old('amount')) ?>" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="payment_date">Payment date <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" id="payment_date" name="payment_date" value="<?= esc(old('payment_date', date('Y-m-d'))) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="payment_method">Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <?php foreach ($methods as $method): ?>
                                    <option value="<?= esc($method) ?>" <?= old('payment_method', 'upi') === $method ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $method))) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold" for="reference_no">Transaction / receipt reference</label>
                        <input class="form-control" id="reference_no" name="reference_no" value="<?= esc(old('reference_no')) ?>" maxlength="100">
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold" for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" maxlength="2000"><?= esc(old('notes')) ?></textarea>
                    </div>
                    <button class="btn btn-success w-100 mt-4" type="submit"><i class="bi bi-check2-circle me-1"></i> Record payment</button>
                </form>
            </section>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
