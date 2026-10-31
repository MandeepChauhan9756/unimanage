<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table            = 'admissions_leads';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'student_name', 'email', 'phone', 'city', 'country', 'study_level',
        'interested_program', 'preferred_university', 'intake', 'lead_source',
        'status', 'assigned_to', 'created_by', 'next_follow_up', 'notes',
        'student_id', 'converted_at',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    // Purpose: Builds a lead query filtered by search text, stage, source, and counselor.
    public function filtered(array $filters = [])
    {
        $builder = $this->select('admissions_leads.*, counselor.name AS counselor_name')
            ->join('users AS counselor', 'counselor.id = admissions_leads.assigned_to', 'left');

        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('admissions_leads.student_name', $filters['search'])
                ->orLike('admissions_leads.email', $filters['search'])
                ->orLike('admissions_leads.phone', $filters['search'])
                ->orLike('admissions_leads.interested_program', $filters['search'])
                ->orLike('admissions_leads.preferred_university', $filters['search'])
                ->groupEnd();
        }

        if (! empty($filters['status'])) {
            $builder->where('admissions_leads.status', $filters['status']);
        }

        if (! empty($filters['lead_source'])) {
            $builder->where('admissions_leads.lead_source', $filters['lead_source']);
        }

        if (! empty($filters['assigned_to'])) {
            $builder->where('admissions_leads.assigned_to', $filters['assigned_to']);
        }

        return $builder->orderBy('admissions_leads.updated_at', 'DESC');
    } // End of filtered()

    // Purpose: Returns a lead with counselor, creator, and converted-student details.
    public function withCounselor(int $id): ?array
    {
        return $this->select('admissions_leads.*, counselor.name AS counselor_name, creator.name AS creator_name, students.enrollment_no')
            ->join('users AS counselor', 'counselor.id = admissions_leads.assigned_to', 'left')
            ->join('users AS creator', 'creator.id = admissions_leads.created_by', 'left')
            ->join('students', 'students.id = admissions_leads.student_id', 'left')
            ->find($id);
    } // End of withCounselor()

    // Purpose: Calculates lead totals by stage and the number of overdue follow-ups.
    public function dashboardMetrics(): array
    {
        $row = $this->db->table($this->table)
            ->select("COUNT(*) AS total, SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS new_leads, SUM(CASE WHEN status = 'counseling' THEN 1 ELSE 0 END) AS counseling, SUM(CASE WHEN status = 'application' THEN 1 ELSE 0 END) AS applications, SUM(CASE WHEN status = 'enrolled' THEN 1 ELSE 0 END) AS enrolled", false)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        $overdue = $this->db->table($this->table)
            ->where('deleted_at', null)
            ->where('next_follow_up <', date('Y-m-d H:i:s'))
            ->whereNotIn('status', ['enrolled', 'lost'])
            ->countAllResults();

        return [
            'total'        => (int) ($row['total'] ?? 0),
            'new'          => (int) ($row['new_leads'] ?? 0),
            'counseling'   => (int) ($row['counseling'] ?? 0),
            'applications' => (int) ($row['applications'] ?? 0),
            'enrolled'     => (int) ($row['enrolled'] ?? 0),
            'overdue'      => $overdue,
        ];
    } // End of dashboardMetrics()

    // Purpose: Returns the number of active leads in each pipeline stage.
    public function pipelineCounts(): array
    {
        $counts = $this->db->table($this->table)
            ->select('status, COUNT(*) AS total')
            ->where('deleted_at', null)
            ->groupBy('status')
            ->get()
            ->getResultArray();
        $result = [];

        foreach ($counts as $row) {
            $result[$row['status']] = (int) $row['total'];
        }

        return $result;
    } // End of pipelineCounts()

    // Purpose: Returns upcoming lead follow-ups ordered by their scheduled time.
    public function followUps(int $limit = 6): array
    {
        return $this->select('admissions_leads.*, counselor.name AS counselor_name')
            ->join('users AS counselor', 'counselor.id = admissions_leads.assigned_to', 'left')
            ->where('admissions_leads.next_follow_up !=', null)
            ->whereNotIn('admissions_leads.status', ['enrolled', 'lost'])
            ->orderBy('admissions_leads.next_follow_up', 'ASC')
            ->findAll($limit);
    } // End of followUps()

    // Purpose: Returns the most recently created leads with counselor names.
    public function recentLeads(int $limit = 6): array
    {
        return $this->select('admissions_leads.*, counselor.name AS counselor_name')
            ->join('users AS counselor', 'counselor.id = admissions_leads.assigned_to', 'left')
            ->orderBy('admissions_leads.created_at', 'DESC')
            ->findAll($limit);
    } // End of recentLeads()
}
