<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Public routes
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::store');

// Dashboard route (protected by auth filter)
$routes->get('dashboard', 'Home::dashboard', ['filter' => 'auth']);

// Course routes
$routes->get('courses', 'Course::index');
$routes->get('courses/search', 'Course::search');
$routes->post('courses/search', 'Course::search');
$routes->post('course/enroll', 'Course::enroll', ['filter' => 'auth']);

// Materials routes
$routes->get('admin/course/(:num)/upload', 'Materials::upload/$1', ['filter' => 'auth']);
$routes->post('admin/course/(:num)/upload', 'Materials::upload/$1', ['filter' => 'auth']);
$routes->get('materials/delete/(:num)', 'Materials::delete/$1', ['filter' => 'auth']);
$routes->get('materials/download/(:num)', 'Materials::download/$1', ['filter' => 'auth']);

// Notifications routes
$routes->get('notifications', 'Notifications::get', ['filter' => 'auth']);
$routes->post('notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1', ['filter' => 'auth']);

// Additional protected routes (will be implemented later)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Admin routes
    $routes->group('admin', function($routes) {
        $routes->get('users', 'Admin::users');
        $routes->post('users/add', 'Admin::addUser');
        $routes->post('users/update-role', 'Admin::updateRole');
        $routes->post('users/toggle-status', 'Admin::toggleStatus');
        $routes->post('users/change-password', 'Admin::changePassword');
        $routes->get('courses', 'Admin::courses');
        $routes->post('courses/add', 'Admin::addCourse');
        $routes->post('courses/update', 'Admin::updateCourse');
        $routes->post('courses/update-status', 'Admin::updateCourseStatus');
        $routes->get('settings', 'Admin::settings');
    });
    
    // Teacher routes
    $routes->group('teacher', function($routes) {
        $routes->get('students', 'Teacher::students');
        $routes->post('students/enroll', 'Teacher::enrollStudent');
        $routes->get('enrollments', 'Teacher::enrollments');
        $routes->post('enrollments/update/(:num)/(:segment)', 'Teacher::updateEnrollmentStatus/$1/$2');
    });
    
    // Student routes
    $routes->group('student', function($routes) {
        $routes->get('enrollments', 'Student::enrollments');
        $routes->get('grades', 'Student::grades');
        $routes->get('progress', 'Student::progress');
    });
});