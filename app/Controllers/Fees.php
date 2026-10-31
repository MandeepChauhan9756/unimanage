<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\FeeInvoiceModel;
use App\Models\FeePaymentModel;
use App\Models\StudentModel;

class Fees extends BaseController
{
    private const PAYMENT_METHODS = ['cash', 'bank_transfer', 'card', 'upi', 'cheque', 'other'];

    // Purpose: Displays fee invoices, balances, metrics, and search filters.
    public function index()
    {
        $filters = [
            'search' => trim((string) $this->request->getGet('search')),
            'status' => (string) $this->request->getGet('status'),
        ];
        $invoiceModel = new FeeInvoiceModel();

        return view('fees/index', [
            'title'   => 'Fee management',
            'metrics' => $invoiceModel->dashboardMetrics(),
            'invoices'=> $invoiceModel->withBalances($filters)->paginate(15),
            'pager'   => $invoiceModel->pager,
            'filters' => $filters,
        ]);
    } 
    // End of index()

    // Purpose: Exports the filtered fee invoices and balances as a CSV download.
    public function exportCsv()
    {
        $filters = [
            'search' => trim((string) $this->request->getGet('search')),
            'status' => (string) $this->request->getGet('status'),
        ];
        $invoices = (new FeeInvoiceModel())->withBalances($filters)->findAll();
        $filename = 'fee_invoices_' . date('Y-m-d') . '.csv';
        $output = fopen('php://temp', 'r+');
        if ($output === false) {
            throw new \RuntimeException('Could not prepare fee export.');
        }

        fputcsv($output, ['Invoice No', 'Student', 'Enrollment No', 'Email', 'Department', 'Title', 'Due Date', 'Amount', 'Paid', 'Balance', 'Status']);
        foreach ($invoices as $invoice) {
            fputcsv($output, [
                $invoice['invoice_no'],
                $invoice['student_name'],
                $invoice['enrollment_no'],
                $invoice['email'],
                $invoice['department_name'],
                $invoice['title'],
                $invoice['due_date'],
                $invoice['amount'],
                $invoice['paid_amount'],
                $invoice['balance_amount'],
                $invoice['fee_status'],
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        if ($csv === false) {
            throw new \RuntimeException('Could not read the generated fee export.');
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    } 
    // End of exportCsv()

    // Purpose: Displays the fee invoice form with active students.
    public function create()
    {
        return view('fees/create', [
            'title'    => 'Create fee invoice',
            'students' => (new StudentModel())->getStudentsWithDetails(['status' => 'active'])->findAll(),
        ]);
    } 
    // End of create()

    // Purpose: Validates and creates a fee invoice for an active student.
    public function store()
    {
        $rules = [
            'student_id' => 'required|integer',
            'title'      => 'required|min_length[3]|max_length[150]',
            'description'=> 'permit_empty|max_length[2000]',
            'amount'     => 'required|decimal|greater_than[0]|less_than_equal_to[9999999999.99]',
            'due_date'   => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $student = (new StudentModel())->getStudentDetail((int) $this->request->getPost('student_id'));
        if (! $student || $student['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Choose an active enrolled student.');
        }

        $db = \Config\Database::connect();
        $currentYear = $db->table('academic_years')->where('is_current', 1)->get()->getRowArray();
        $invoiceNo = 'FEE-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
        $invoiceModel = new FeeInvoiceModel();
        $invoiceId = $invoiceModel->insert([
            'invoice_no'      => $invoiceNo,
            'student_id'      => (int) $student['id'],
            'title'           => trim((string) $this->request->getPost('title')),
            'description'     => trim((string) $this->request->getPost('description')) ?: null,
            'amount'          => number_format((float) $this->request->getPost('amount'), 2, '.', ''),
            'due_date'        => $this->request->getPost('due_date'),
            'academic_year_id'=> $currentYear['id'] ?? null,
            'created_by'      => (int) session()->get('user_id'),
        ], true);

        if (! $invoiceId) {
            return redirect()->back()->withInput()->with('error', 'The fee invoice could not be created. Please try again.');
        }

        (new AuditLogModel())->logAction((int) session()->get('user_id'), 'CREATE_FEE_INVOICE', 'Created invoice ' . $invoiceNo . ' for ' . $student['enrollment_no']);

        return redirect()->to('/fees/invoice/' . $invoiceId)->with('success', 'Fee invoice created successfully.');
    } 
    // End of store()

    // Purpose: Displays an invoice, its balance, and payment history to an authorized viewer.
    public function invoice(int $id)
    {
        $invoice = (new FeeInvoiceModel())->invoiceWithBalance($id);
        if (! $invoice) {
            return redirect()->to('/fees')->with('error', 'Fee invoice not found.');
        }

        if (session()->get('role') === 'student' && (int) $invoice['student_id'] !== $this->currentStudentId()) {
            return redirect()->to('/fees/my-fees')->with('error', 'You are not allowed to view that invoice.');
        }

        return view('fees/invoice', [
            'title'       => 'Invoice ' . $invoice['invoice_no'],
            'invoice'     => $invoice,
            'payments'    => (new FeeInvoiceModel())->paymentsForInvoice($id),
            'methods'     => self::PAYMENT_METHODS,
        ]);
    } 
    // End of invoice()

    // Purpose: Validates and records a payment that does not exceed the invoice balance.
    public function recordPayment(int $id)
    {
        $rules = [
            'amount'         => 'required|decimal|greater_than[0]|less_than_equal_to[9999999999.99]',
            'payment_date'   => 'required|valid_date[Y-m-d]',
            'payment_method' => 'required|in_list[' . implode(',', self::PAYMENT_METHODS) . ']',
            'reference_no'   => 'permit_empty|max_length[100]',
            'notes'          => 'permit_empty|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $invoiceModel = new FeeInvoiceModel();
            if ($db->DBDriver === 'MySQLi') {
                $db->query('SELECT id FROM fee_invoices WHERE id = ? FOR UPDATE', [$id]);
            }
            $invoice = $invoiceModel->where('id', $id)->first();
            if (! $invoice) {
                $db->transRollback();
                return redirect()->to('/fees')->with('error', 'Fee invoice not found.');
            }

            $paidAmount = (float) ($db->table('fee_payments')
                ->selectSum('amount', 'paid_amount')
                ->where('invoice_id', $id)
                ->get()
                ->getRow('paid_amount') ?? 0);
            $balance = round((float) $invoice['amount'] - $paidAmount, 2);
            $paymentAmount = round((float) $this->request->getPost('amount'), 2);

            if ($balance <= 0) {
                $db->transRollback();
                return redirect()->to('/fees/invoice/' . $id)->with('error', 'This invoice is already fully paid.');
            }
            if ($paymentAmount > $balance) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Payment cannot exceed the remaining balance of ' . number_format($balance, 2) . '.');
            }

            $paymentId = (new FeePaymentModel())->insert([
                'invoice_id'    => $id,
                'amount'        => number_format($paymentAmount, 2, '.', ''),
                'payment_date'  => $this->request->getPost('payment_date'),
                'payment_method'=> $this->request->getPost('payment_method'),
                'reference_no'  => trim((string) $this->request->getPost('reference_no')) ?: null,
                'notes'         => trim((string) $this->request->getPost('notes')) ?: null,
                'received_by'   => (int) session()->get('user_id'),
                'created_at'    => date('Y-m-d H:i:s'),
            ], true);

            if (! $paymentId) {
                throw new \RuntimeException('Payment could not be recorded.');
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Payment transaction failed.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Fee payment failed for invoice {invoiceId}: {message}', [
                'invoiceId' => $id,
                'message'   => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', 'The payment could not be recorded. Please try again.');
        }

        (new AuditLogModel())->logAction((int) session()->get('user_id'), 'RECORD_FEE_PAYMENT', 'Recorded payment of ' . number_format($paymentAmount, 2) . ' for invoice ID ' . $id);

        return redirect()->to('/fees/invoice/' . $id)->with('success', 'Payment recorded successfully.');
    } 
    // End of recordPayment()

    // Purpose: Displays fee invoices belonging to the signed-in student.
    public function myFees()
    {
        $studentId = $this->currentStudentId();
        if (! $studentId) {
            return redirect()->to('/dashboard')->with('error', 'Your student fee account is not linked yet. Please contact the university administrator.');
        }

        $invoiceModel = new FeeInvoiceModel();
        return view('fees/my_fees', [
            'title'    => 'My fees',
            'summary'  => $invoiceModel->studentSummary($studentId),
            'invoices' => $invoiceModel->withBalances(['student_id' => $studentId])->findAll(),
        ]);
    } 
    // End of myFees()

    // Purpose: Finds the student record associated with the signed-in user.
    private function currentStudentId(): ?int
    {
        $student = (new StudentModel())->where('user_id', session()->get('user_id'))->first();
        return $student ? (int) $student['id'] : null;
    } 
    // End of currentStudentId()
}
