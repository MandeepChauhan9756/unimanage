<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->post('logout', 'Auth::logout');

// Purpose: Registers routes available to signed-in users.
$routes->group(
    '',
    ['filter' => 'auth'],
    static function ($routes) {
        $routes->get('dashboard', 'Dashboard::index');
    } 
    // End of anonymous callback
);

// Admissions CRM — counselors and administrators manage the lead pipeline.
// Purpose: Registers admissions lead routes and their role restrictions.
$routes->group(
    'leads',
    ['filter' => 'auth'],
    static function ($routes) {
        $routes->get('', 'Leads::index', ['filter' => 'role:admin,faculty']);
        $routes->get('create', 'Leads::create', ['filter' => 'role:admin,faculty']);
        $routes->post('store', 'Leads::store', ['filter' => 'role:admin,faculty']);
        $routes->get('profile/(:num)', 'Leads::profile/$1', ['filter' => 'role:admin,faculty']);
        $routes->get('convert/(:num)', 'Leads::convert/$1', ['filter' => 'role:admin,faculty']);
        $routes->post('convert/(:num)', 'Leads::convertStore/$1', ['filter' => 'role:admin,faculty']);
        $routes->get('edit/(:num)', 'Leads::edit/$1', ['filter' => 'role:admin,faculty']);
        $routes->post('update/(:num)', 'Leads::update/$1', ['filter' => 'role:admin,faculty']);
        $routes->post('activity/(:num)', 'Leads::addActivity/$1', ['filter' => 'role:admin,faculty']);
    } 
    // End of anonymous callback
);

// Student Management Routes (Admin & authorized staff)
// Purpose: Registers student management and profile routes with role restrictions.
$routes->group(
    'students',
    ['filter' => 'auth'],
    static function ($routes) {
        $routes->get('', 'Student::index', ['filter' => 'role:admin,faculty,accountant']);
        $routes->get('create', 'Student::create', ['filter' => 'role:admin']);
        $routes->post('store', 'Student::store', ['filter' => 'role:admin']);
        $routes->get('export', 'Student::exportCsv', ['filter' => 'role:admin']);
        $routes->get('my-profile', 'Student::myProfile', ['filter' => 'role:student']);
        $routes->get('profile/(:num)', 'Student::profile/$1', ['filter' => 'role:admin,faculty,accountant,student']);
        $routes->post('toggle-status/(:num)', 'Student::toggleStatus/$1', ['filter' => 'role:admin']);
        $routes->post('reset-password/(:num)', 'Student::resetPassword/$1', ['filter' => 'role:admin']);
    } 
    // End of anonymous callback
);

// Fee invoices and payments — accountants administer balances; students can view their own fees.
// Purpose: Registers fee invoice and payment routes with role restrictions.
$routes->group(
    'fees',
    ['filter' => 'auth'],
    static function ($routes) {
        $routes->get('', 'Fees::index', ['filter' => 'role:admin,accountant']);
        $routes->get('create', 'Fees::create', ['filter' => 'role:admin,accountant']);
        $routes->get('export', 'Fees::exportCsv', ['filter' => 'role:admin,accountant']);
        $routes->post('store', 'Fees::store', ['filter' => 'role:admin,accountant']);
        $routes->get('invoice/(:num)', 'Fees::invoice/$1', ['filter' => 'role:admin,accountant,student']);
        $routes->post('invoice/(:num)/payment', 'Fees::recordPayment/$1', ['filter' => 'role:admin,accountant']);
        $routes->get('my-fees', 'Fees::myFees', ['filter' => 'role:student']);
    } 
    // End of anonymous callback
);

// Academic Structure (Admin & Faculty)
// Purpose: Registers academic department, course, and subject routes.
$routes->group(
    'academics',
    ['filter' => 'auth'],
    static function ($routes) {
        // Departments
        $routes->get('departments', 'Academics::departments', ['filter' => 'role:admin,faculty']);
        $routes->post('departments/store', 'Academics::storeDepartment', ['filter' => 'role:admin']);

        // Courses
        $routes->get('courses', 'Academics::courses', ['filter' => 'role:admin,faculty']);
        $routes->post('courses/store', 'Academics::storeCourse', ['filter' => 'role:admin']);

        // Subjects
        $routes->get('subjects', 'Academics::subjects', ['filter' => 'role:admin,faculty']);
        $routes->post('subjects/store', 'Academics::storeSubject', ['filter' => 'role:admin']);
    } 
    // End of anonymous callback
);

// Faculty Management
// Purpose: Registers faculty management and subject assignment routes.
$routes->group(
    'faculty',
    ['filter' => 'auth'],
    static function ($routes) {
        $routes->get('', 'FacultyController::index', ['filter' => 'role:admin,faculty']);
        $routes->get('create', 'FacultyController::create', ['filter' => 'role:admin']);
        $routes->post('store', 'FacultyController::store', ['filter' => 'role:admin']);
        $routes->get('assign-subject', 'FacultyController::assignSubject', ['filter' => 'role:admin']);
        $routes->post('store-assignment', 'FacultyController::storeAssignment', ['filter' => 'role:admin']);
    } 
    // End of anonymous callback
);

// Attendance Routes
// Purpose: Registers attendance viewing and recording routes.
$routes->group(
    'attendance',
    ['filter' => 'auth'],
    static function ($routes) {
        $routes->get('', 'Attendance::index', ['filter' => 'role:admin,faculty']);
        $routes->get('mark', 'Attendance::markView', ['filter' => 'role:admin,faculty']);
        $routes->get('mark-student', 'Attendance::markStudentView', ['filter' => 'role:admin,faculty']);
        $routes->post('store', 'Attendance::store', ['filter' => 'role:admin,faculty']);
        $routes->post('store-student', 'Attendance::storeStudent', ['filter' => 'role:admin,faculty']);
        $routes->get('report', 'Attendance::report', ['filter' => 'role:admin,faculty']);
        $routes->get('my-attendance', 'Attendance::myAttendance', ['filter' => 'role:student']);
    } 
    // End of anonymous callback
);

// Timetable Routes
// Purpose: Registers timetable viewing and management routes.
$routes->group(
    'timetable',
    ['filter' => 'auth'],
    static function ($routes) {
        $routes->get('', 'Timetable::index');
        $routes->get('create', 'Timetable::create', ['filter' => 'role:admin']);
        $routes->post('store', 'Timetable::store', ['filter' => 'role:admin']);
    } 
    // End of anonymous callback
);
