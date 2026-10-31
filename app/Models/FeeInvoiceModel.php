<?php

namespace App\Models;

use CodeIgniter\Model;

class FeeInvoiceModel extends Model
{
    protected $table            = 'fee_invoices';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'invoice_no', 'student_id', 'title', 'description', 'amount',
        'due_date', 'academic_year_id', 'created_by',
    ];
    protected $useTimestamps    = true;

    // Purpose: Builds a filtered invoice query with payment totals and calculated balances.
    public function withBalances(array $filters = [])
    {
        $builder = $this->select(
            "fee_invoices.*, students.enrollment_no, users.name AS student_name, users.email, departments.name AS department_name, academic_years.year_label, COALESCE(payments.paid_amount, 0) AS paid_amount, GREATEST(fee_invoices.amount - COALESCE(payments.paid_amount, 0), 0) AS balance_amount, CASE WHEN fee_invoices.amount <= COALESCE(payments.paid_amount, 0) THEN 'paid' WHEN fee_invoices.due_date < '" . date('Y-m-d') . "' THEN 'overdue' WHEN COALESCE(payments.paid_amount, 0) > 0 THEN 'partial' ELSE 'unpaid' END AS fee_status",
            false
        )
            ->join('students', 'students.id = fee_invoices.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('departments', 'departments.id = students.department_id')
            ->join('academic_years', 'academic_years.id = fee_invoices.academic_year_id', 'left')
            ->join('(SELECT invoice_id, SUM(amount) AS paid_amount FROM fee_payments GROUP BY invoice_id) payments', 'payments.invoice_id = fee_invoices.id', 'left', false);

        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('fee_invoices.invoice_no', $filters['search'])
                ->orLike('users.name', $filters['search'])
                ->orLike('users.email', $filters['search'])
                ->orLike('students.enrollment_no', $filters['search'])
                ->groupEnd();
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'overdue') {
                $builder->where('fee_invoices.due_date <', date('Y-m-d'))
                    ->where('fee_invoices.amount > COALESCE(payments.paid_amount, 0)', null, false);
            } elseif ($filters['status'] === 'paid') {
                $builder->where('fee_invoices.amount <= COALESCE(payments.paid_amount, 0)', null, false);
            } elseif ($filters['status'] === 'partial') {
                $builder->where('COALESCE(payments.paid_amount, 0) > 0', null, false)
                    ->where('fee_invoices.amount > COALESCE(payments.paid_amount, 0)', null, false);
            } elseif ($filters['status'] === 'unpaid') {
                $builder->where('COALESCE(payments.paid_amount, 0) = 0', null, false)
                    ->where('fee_invoices.due_date >=', date('Y-m-d'));
            }
        }

        if (! empty($filters['student_id'])) {
            $builder->where('fee_invoices.student_id', $filters['student_id']);
        }

        return $builder->orderBy('fee_invoices.due_date', 'ASC')->orderBy('fee_invoices.id', 'DESC');
    } // End of withBalances()

    // Purpose: Returns one invoice with its payment total, balance, and status.
    public function invoiceWithBalance(int $id): ?array
    {
        return $this->withBalances()->where('fee_invoices.id', $id)->first();
    } // End of invoiceWithBalance()

    // Purpose: Calculates overall invoice, billed, collected, outstanding, and overdue totals.
    public function dashboardMetrics(): array
    {
        $row = $this->db->table('fee_invoices invoices')
            ->select("COUNT(DISTINCT invoices.id) AS invoice_count, COALESCE(SUM(invoices.amount), 0) AS billed, COALESCE(SUM(COALESCE(payments.paid_amount, 0)), 0) AS collected, COALESCE(SUM(GREATEST(invoices.amount - COALESCE(payments.paid_amount, 0), 0)), 0) AS outstanding, SUM(CASE WHEN invoices.due_date < '" . date('Y-m-d') . "' AND invoices.amount > COALESCE(payments.paid_amount, 0) THEN 1 ELSE 0 END) AS overdue_count", false)
            ->join('(SELECT invoice_id, SUM(amount) AS paid_amount FROM fee_payments GROUP BY invoice_id) payments', 'payments.invoice_id = invoices.id', 'left', false)
            ->get()
            ->getRowArray();

        return [
            'invoice_count' => (int) ($row['invoice_count'] ?? 0),
            'billed'        => (float) ($row['billed'] ?? 0),
            'collected'     => (float) ($row['collected'] ?? 0),
            'outstanding'   => (float) ($row['outstanding'] ?? 0),
            'overdue_count' => (int) ($row['overdue_count'] ?? 0),
        ];
    } // End of dashboardMetrics()

    // Purpose: Returns payment records for an invoice with the receiving user name.
    public function paymentsForInvoice(int $invoiceId): array
    {
        return $this->db->table('fee_payments')
            ->select('fee_payments.*, users.name AS received_by_name')
            ->join('users', 'users.id = fee_payments.received_by', 'left')
            ->where('fee_payments.invoice_id', $invoiceId)
            ->orderBy('fee_payments.payment_date', 'DESC')
            ->orderBy('fee_payments.id', 'DESC')
            ->get()
            ->getResultArray();
    } // End of paymentsForInvoice()

    // Purpose: Calculates total billed, paid, and remaining balances for a student.
    public function studentSummary(int $studentId): array
    {
        return $this->db->table('fee_invoices invoices')
            ->select("COALESCE(SUM(invoices.amount), 0) AS billed, COALESCE(SUM(COALESCE(payments.paid_amount, 0)), 0) AS paid, COALESCE(SUM(GREATEST(invoices.amount - COALESCE(payments.paid_amount, 0), 0)), 0) AS balance", false)
            ->join('(SELECT invoice_id, SUM(amount) AS paid_amount FROM fee_payments GROUP BY invoice_id) payments', 'payments.invoice_id = invoices.id', 'left', false)
            ->where('invoices.student_id', $studentId)
            ->get()
            ->getRowArray() ?? ['billed' => 0, 'paid' => 0, 'balance' => 0];
    } // End of studentSummary()
}
