<?php

namespace App\Models;

use CodeIgniter\Model;

class TimetableModel extends Model
{
    protected $table            = 'timetables';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['course_id', 'semester', 'subject_id', 'faculty_id', 'academic_year_id', 'classroom', 'day_of_week', 'start_time', 'end_time'];
    protected $useTimestamps    = true;

    // Purpose: Returns timetable entries with course, subject, and faculty names and optional filters.
    public function getTimetableWithDetails($filters = [])
    {
        $builder = $this->select('timetables.*, courses.name as course_name, subjects.name as subject_name, users.name as faculty_name')
                        ->join('courses', 'courses.id = timetables.course_id')
                        ->join('subjects', 'subjects.id = timetables.subject_id')
                        ->join('faculty', 'faculty.id = timetables.faculty_id')
                        ->join('users', 'users.id = faculty.user_id');

        if (!empty($filters['course_id'])) {
            $builder->where('timetables.course_id', $filters['course_id']);
        }
        if (!empty($filters['semester'])) {
            $builder->where('timetables.semester', $filters['semester']);
        }
        if (!empty($filters['day_of_week'])) {
            $builder->where('timetables.day_of_week', $filters['day_of_week']);
        }

        return $builder->orderBy('FIELD(day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday")', '', false)
                       ->orderBy('start_time', 'ASC')
                       ->findAll();
    } // End of getTimetableWithDetails()
}