<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\EnrollmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informacion', [HomeController::class, 'about'])->name('about');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Public Course Routes
|--------------------------------------------------------------------------
*/

Route::get('/cursos', [CourseController::class, 'index'])->name('courses.index');
Route::get('/cursos/{course}', [CourseController::class, 'show'])->name('courses.show');

/*
|--------------------------------------------------------------------------
| Student Routes (authenticated students only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'student'])->group(function () {
    Route::post('/cursos/{course}/matricular', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::get('/mis-matriculas', [CourseController::class, 'myEnrollments'])->name('student.enrollments');
    Route::get('/mi-perfil', [CourseController::class, 'profile'])->name('student.profile');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (authenticated administrators only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('administracion')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Students CRUD
    Route::resource('alumnos', StudentController::class)->names([
        'index' => 'students.index',
        'create' => 'students.create',
        'store' => 'students.store',
        'show' => 'students.show',
        'edit' => 'students.edit',
        'update' => 'students.update',
        'destroy' => 'students.destroy',
    ])->parameters(['alumnos' => 'student']);

    // Courses CRUD
    Route::resource('cursos', AdminCourseController::class)->names([
        'index' => 'courses.index',
        'create' => 'courses.create',
        'store' => 'courses.store',
        'show' => 'courses.show',
        'edit' => 'courses.edit',
        'update' => 'courses.update',
        'destroy' => 'courses.destroy',
    ])->parameters(['cursos' => 'course']);

    // Enrollments
    Route::get('/matriculas', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/matriculas/crear', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/matriculas', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::patch('/matriculas/{enrollment}/cancelar', [EnrollmentController::class, 'cancel'])->name('enrollments.cancel');
    Route::patch('/matriculas/{enrollment}/reactivar', [EnrollmentController::class, 'reactivate'])->name('enrollments.reactivate');
});
