<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Student portal</div>
    <h1 class="page-title mb-1">My fees</h1>
    <p class="text-muted mb-0">Review your invoices, due dates, payments and remaining balance.</p>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['Total billed', $summary['billed'], 'primary'],
        ['Total paid', $summary['paid'], 'success'],
        ['Balance due', $summary['balance'], 'warning'],
    ] as [$label, $amount, $color]): ?>
        <div class="col-md-4">
            <section class="card card-soft p-3 p-lg-4 h-100">
                <div class="text-muted fs-7 mb-2"><?= esc($label) ?></div>
                <div class="h4 fw-bold text-<?= esc($color) ?> mb-0">₹<?= esc(number_format((float) $amount, 2)) ?></div>
            </section>
        </div>
    <?php endforeach; ?>
</div>

<section class="card card-soft">
    <div class="p-3 p-lg-4 border-bottom">
        <h2 class="h5 fw-bold mb-1">My invoices</h2>
        <p class="text-muted fs-7 mb-0">For payment assistance, contact the university accounts office.</p>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-4">Invoice</th><th>Due date</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th><th class="text-end pe-4"> </th></tr></thead>
            <tbody>
            <?php if ($invoices === []): ?>
                <tr><td colspan="7" class="text-center text-muted py-5">There are no fee invoices for your student account yet.</td></tr>
            <?php else: foreach ($invoices as $invoice): ?>
                <?php
                    $statusColors = ['paid' => 'success', 'partial' => 'info', 'overdue' => 'danger', 'unpaid' => 'warning'];
                    $statusColor = $statusColors[$invoice['fee_status']] ?? 'secondary';
                ?>
                <tr>
                    <td class="ps-4"><div class="fw-semibold"><?= esc($invoice['title']) ?></div><div class="text-muted fs-7"><?= esc($invoice['invoice_no']) ?></div></td>
                    <td><?= esc(date('M j, Y', strtotime($invoice['due_date']))) ?></td>
                    <td>₹<?= esc(number_format((float) $invoice['amount'], 2)) ?></td>
                    <td class="text-success">₹<?= esc(number_format((float) $invoice['paid_amount'], 2)) ?></td>
                    <td class="fw-semibold">₹<?= esc(number_format((float) $invoice['balance_amount'], 2)) ?></td>
                    <td><span class="badge bg-<?= esc($statusColor) ?>-subtle text-<?= esc($statusColor) ?> border border-<?= esc($statusColor) ?>-subtle rounded-pill"><?= esc(ucfirst($invoice['fee_status'])) ?></span></td>
                    <td class="text-end pe-4"><a class="btn btn-sm btn-light" href="<?= base_url('fees/invoice/' . $invoice['id']) ?>">Details</a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
