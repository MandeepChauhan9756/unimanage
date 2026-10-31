<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadActivityModel extends Model
{
    protected $table            = 'lead_activities';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['lead_id', 'user_id', 'activity_type', 'details', 'created_at'];
    protected $useTimestamps    = false;

    // Purpose: Returns a lead’s activity timeline with the name of each recording user.
    public function forLead(int $leadId): array
    {
        return $this->select('lead_activities.*, users.name AS user_name')
            ->join('users', 'users.id = lead_activities.user_id', 'left')
            ->where('lead_id', $leadId)
            ->orderBy('lead_activities.created_at', 'DESC')
            ->findAll();
    } // End of forLead()
}
