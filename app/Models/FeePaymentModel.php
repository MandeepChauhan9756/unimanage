<?php

namespace App\Models;

use CodeIgniter\Model;

class FeePaymentModel extends Model
{
    protected $table            = 'fee_payments';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'invoice_id', 'amount', 'payment_date', 'payment_method',
        'reference_no', 'notes', 'received_by',
    ];
    protected $useTimestamps    = false;
}
