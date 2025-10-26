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
$routes->get('dashboard', 'Auth::dashboard', ['filter' => 'auth']);

// Additional protected routes (will be implemented later)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Admin routes
    $routes->group('admin', function($routes) {
        $routes->get('users', 'Admin::users');
        $routes->get('courses', 'Admin::courses');
        $routes->get('settings', 'Admin::settings');
    });
    
    // Teacher routes
    $routes->group('teacher', function($routes) {
        $routes->get('courses', 'Teacher::courses');
        $routes->get('quizzes', 'Teacher::quizzes');
        $routes->get('grades', 'Teacher::grades');
    });
    
    // Student routes
    $routes->group('student', function($routes) {
        $routes->get('enrollments', 'Student::enrollments');
        $routes->get('grades', 'Student::grades');
        $routes->get('progress', 'Student::progress');
    });
});