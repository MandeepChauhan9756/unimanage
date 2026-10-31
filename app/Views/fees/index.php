<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Finance workspace</div>
        <h1 class="page-title mb-1">Fees & payments</h1>
        <p class="text-muted mb-0">Manage student invoices, record payments and follow outstanding balances.</p>
    </div>
    <div class="d-flex gap-2">
        <?php $exportQuery = http_build_query(array_filter($filters, static /* Purpose: Removes empty filters from the fee export query. */ fn ($value) => $value !== '' /* End of arrow callback */)); ?>
        <a href="<?= esc(base_url('fees/export' . ($exportQuery ? '?' . $exportQuery : ''))) ?>" class="btn btn-outline-secondary px-3 py-2"><i class="bi bi-download me-1"></i> Export CSV</a>
        <a href="<?= base_url('fees/create') ?>" class="btn btn-primary px-3 py-2"><i class="bi bi-plus-lg me-1"></i> Create invoice</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Invoices', number_format($metrics['invoice_count']), 'bi-receipt', 'primary'],
        ['Total billed', '₹' . number_format($metrics['billed'], 2), 'bi-journal-text', 'info'],
        ['Collected', '₹' . number_format($metrics['collected'], 2), 'bi-cash-stack', 'success'],
        ['Outstanding', '₹' . number_format($metrics['outstanding'], 2), 'bi-hourglass-split', 'warning'],
        ['Overdue invoices', number_format($metrics['overdue_count']), 'bi-alarm', 'danger'],
    ];
    foreach ($cards as [$label, $value, $icon, $color]): ?>
        <div class="col-6 col-xl">
            <section class="card card-soft h-100 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fs-7"><?= esc($label) ?></span>
                    <i class="bi <?= esc($icon) ?> text-<?= esc($color) ?>"></i>
                </div>
                <div class="h5 fw-bold mb-0"><?= esc($value) ?></div>
            </section>
        </div>
    <?php endforeach; ?>
</div>

<section class="card card-soft">
    <div class="p-3 border-bottom">
        <form method="get" action="<?= base_url('fees') ?>" class="row g-2">
            <div class="col-md-7">
                <input class="form-control" type="search" name="search" value="<?= esc($filters['search']) ?>" placeholder="Search invoice, student, email or enrollment number">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All payment statuses</option>
                    <?php foreach (['unpaid', 'partial', 'overdue', 'paid'] as $status): ?>
                        <option value="<?= esc($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit">Filter</button>
                <a class="btn btn-light" href="<?= base_url('fees') ?>">Clear</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-4">Invoice</th><th>Student</th><th>Due date</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th><th class="text-end pe-4"> </th></tr></thead>
            <tbody>
            <?php if ($invoices === []): ?>
                <tr><td colspan="8" class="text-center text-muted py-5">No fee invoices found. Create an invoice to get started.</td></tr>
            <?php else: foreach ($invoices as $invoice): ?>
                <?php
                    $statusColors = ['paid' => 'success', 'partial' => 'info', 'overdue' => 'danger', 'unpaid' => 'warning'];
                    $statusColor = $statusColors[$invoice['fee_status']] ?? 'secondary';
                ?>
                <tr>
                    <td class="ps-4">
                        <a class="fw-semibold text-decoration-none" href="<?= base_url('fees/invoice/' . $invoice['id']) ?>"><?= esc($invoice['invoice_no']) ?></a>
                        <div class="text-muted fs-7"><?= esc($invoice['title']) ?></div>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= esc($invoice['student_name']) ?></div>
                        <div class="text-muted fs-7"><?= esc($invoice['enrollment_no']) ?></div>
                    </td>
                    <td><?= esc(date('M j, Y', strtotime($invoice['due_date']))) ?></td>
                    <td>₹<?= esc(number_format((float) $invoice['amount'], 2)) ?></td>
                    <td class="text-success">₹<?= esc(number_format((float) $invoice['paid_amount'], 2)) ?></td>
                    <td class="fw-semibold">₹<?= esc(number_format((float) $invoice['balance_amount'], 2)) ?></td>
                    <td><span class="badge bg-<?= esc($statusColor) ?>-subtle text-<?= esc($statusColor) ?> border border-<?= esc($statusColor) ?>-subtle rounded-pill"><?= esc(ucfirst($invoice['fee_status'])) ?></span></td>
                    <td class="text-end pe-4"><a class="btn btn-sm btn-light" href="<?= base_url('fees/invoice/' . $invoice['id']) ?>">Open</a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pager): ?><div class="px-3 py-2 border-top d-flex justify-content-end"><?= $pager->links('default', 'bootstrap_pagination') ?></div><?php endif; ?>
</section>
<?= $this->endSection() ?>
