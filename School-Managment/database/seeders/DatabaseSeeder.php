<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create administrator
        $admin = User::create([
            'name' => 'Admin',
            'surname' => 'EduCenter',
            'email' => 'admin@educenter.es',
            'password' => 'password',
            'role' => 'admin',
            'is_registered' => true,
        ]);

        // Create students (pre-registered by admin, but not yet activated)
        $student1 = User::create([
            'name' => 'María',
            'surname' => 'García López',
            'email' => 'maria@educenter.es',
            'password' => 'password',
            'role' => 'student',
            'dni' => '12345678A',
            'phone' => '600111222',
            'is_registered' => true, // Already registered
        ]);

        $student2 = User::create([
            'name' => 'Carlos',
            'surname' => 'Martínez Ruiz',
            'email' => 'carlos@educenter.es',
            'password' => 'password',
            'role' => 'student',
            'dni' => '23456789B',
            'phone' => '600333444',
            'is_registered' => true,
        ]);

        $student3 = User::create([
            'name' => 'Ana',
            'surname' => 'Fernández Díaz',
            'email' => 'ana@educenter.es',
            'password' => 'temporary',
            'role' => 'student',
            'dni' => '34567890C',
            'phone' => '600555666',
            'is_registered' => false, // Pending registration
        ]);

        $student4 = User::create([
            'name' => 'Iñaki',
            'surname' => 'Etxeberria Agirre',
            'email' => 'inaki@educenter.es',
            'password' => 'temporary',
            'role' => 'student',
            'dni' => '45678901D',
            'phone' => '600777888',
            'is_registered' => false, // Pending registration
        ]);

        // Create courses
        $course1 = Course::create([
            'name' => 'Desarrollo Web Full Stack',
            'code' => 'DW-001',
            'description' => 'Curso completo de desarrollo web que cubre HTML, CSS, JavaScript, PHP, Laravel y bases de datos. Aprende a crear aplicaciones web profesionales desde cero.',
            'duration_hours' => 600,
            'capacity' => 25,
            'status' => 'active',
            'start_date' => '2026-10-01',
            'end_date' => '2027-06-30',
        ]);

        $course2 = Course::create([
            'name' => 'Diseño UX/UI',
            'code' => 'UX-001',
            'description' => 'Aprende los fundamentos del diseño de experiencia de usuario e interfaces. Herramientas profesionales como Figma, prototipado y testing de usabilidad.',
            'duration_hours' => 300,
            'capacity' => 20,
            'status' => 'active',
            'start_date' => '2026-10-15',
            'end_date' => '2027-03-15',
        ]);

        $course3 = Course::create([
            'name' => 'Administración de Sistemas',
            'code' => 'AS-001',
            'description' => 'Gestión y administración de sistemas informáticos. Linux, Windows Server, redes, seguridad y virtualización.',
            'duration_hours' => 500,
            'capacity' => 20,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-31',
        ]);

        $course4 = Course::create([
            'name' => 'Ciberseguridad',
            'code' => 'CS-001',
            'description' => 'Formación especializada en seguridad informática. Análisis de vulnerabilidades, pentesting, criptografía y respuesta ante incidentes.',
            'duration_hours' => 400,
            'capacity' => 15,
            'status' => 'active',
            'start_date' => '2027-01-15',
            'end_date' => '2027-06-15',
        ]);

        $course5 = Course::create([
            'name' => 'Inteligencia Artificial y Machine Learning',
            'code' => 'IA-001',
            'description' => 'Introducción a la inteligencia artificial, aprendizaje automático, redes neuronales y procesamiento de lenguaje natural con Python.',
            'duration_hours' => 350,
            'capacity' => 18,
            'status' => 'active',
            'start_date' => '2027-02-01',
            'end_date' => '2027-07-31',
        ]);

        $course6 = Course::create([
            'name' => 'Marketing Digital',
            'code' => 'MD-001',
            'description' => 'Estrategias de marketing digital, SEO, SEM, redes sociales, analítica web y campañas publicitarias online.',
            'duration_hours' => 200,
            'capacity' => 30,
            'status' => 'inactive',
            'start_date' => null,
            'end_date' => null,
        ]);

        // Create enrollments
        Enrollment::create([
            'student_id' => $student1->id,
            'course_id' => $course1->id,
            'enrolled_at' => now()->subDays(5),
            'status' => 'active',
        ]);

        Enrollment::create([
            'student_id' => $student1->id,
            'course_id' => $course2->id,
            'enrolled_at' => now()->subDays(3),
            'status' => 'active',
        ]);

        Enrollment::create([
            'student_id' => $student2->id,
            'course_id' => $course1->id,
            'enrolled_at' => now()->subDays(4),
            'status' => 'active',
        ]);

        Enrollment::create([
            'student_id' => $student2->id,
            'course_id' => $course3->id,
            'enrolled_at' => now()->subDays(2),
            'status' => 'active',
        ]);
    }
}
