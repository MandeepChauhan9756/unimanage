<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'action', 'description', 'ip_address', 'user_agent', 'created_at'];
    protected $useTimestamps    = false;

    // Purpose: Stores an audit entry with the user, action, request IP, and user agent.
    public function logAction(?int $userId, string $action, string $description = '')
    {
        $request = \Config\Services::request();
        return $this->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => (string) $request->getUserAgent(),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    } // End of logAction()
}