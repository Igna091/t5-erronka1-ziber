<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email, string $name = 'Test'): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => true,
        ]);
    }

    private function admin(): User
    {
        return $this->user(Role::ADMIN, 'admin@educenter.es');
    }

    // --- Security headers -------------------------------------------------

    public function test_security_headers_are_sent(): void
    {
        $response = $this->get('/login')->assertOk();

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringNotContainsString('unsafe-eval', $csp);
    }

    public function test_hsts_only_over_https(): void
    {
        $this->get('/login')->assertHeaderMissing('Strict-Transport-Security');
        $this->get('https://localhost/login')->assertHeader('Strict-Transport-Security');
    }

    public function test_pages_have_no_inline_javascript_blocked_by_csp(): void
    {
        $admin = $this->admin();
        Course::create(['name' => 'DAW', 'code' => 'DAW-1', 'status' => 'active']);

        $pages = ['/', '/cursos', '/login', '/register'];
        $adminPages = ['/administracion', '/administracion/alumnos', '/administracion/cursos', '/administracion/matriculas'];

        foreach ([...$pages, ...$adminPages] as $url) {
            $html = in_array($url, $adminPages)
                ? $this->actingAs($admin)->get($url)->assertOk()->getContent()
                : $this->get($url)->assertOk()->getContent();

            $this->assertDoesNotMatchRegularExpression('/\son[a-z]+\s*=/i', $html, "Inline event handler in {$url}");
            $this->assertDoesNotMatchRegularExpression('/<script(?![^>]*\bsrc=)[^>]*>/i', $html, "Inline <script> in {$url}");
        }
    }

    // --- Admin search filters -----------------------------------------------

    public function test_array_parameters_do_not_crash_admin_lists(): void
    {
        $this->actingAs($this->admin());

        foreach (['alumnos', 'cursos', 'matriculas'] as $section) {
            $this->get("/administracion/{$section}?search[]=x")->assertRedirect();
            $this->get("/administracion/{$section}?status[]=x")->assertRedirect();
            $this->get("/administracion/{$section}?search=".str_repeat('a', 101))->assertRedirect();
        }
    }

    public function test_enrollment_search_respects_status_filter(): void
    {
        $student = $this->user(Role::STUDENT, 'maria@educenter.es', 'Maria');
        $course = Course::create(['name' => 'DAW', 'code' => 'DAW-1', 'status' => 'active']);
        Enrollment::create(['student_id' => $student->id, 'course_id' => $course->id, 'status' => 'active']);

        $response = $this->actingAs($this->admin())
            ->get('/administracion/matriculas?search=Maria&status=cancelled')
            ->assertOk();

        $this->assertCount(0, $response->viewData('enrollments'));
    }

    // --- Inactive courses -----------------------------------------------------

    public function test_inactive_course_is_hidden_from_public(): void
    {
        $course = Course::create(['name' => 'Oculto', 'code' => 'OC-1', 'status' => 'inactive']);

        $this->get(route('courses.show', $course))->assertNotFound();
        $this->actingAs($this->user(Role::STUDENT, 'otro@educenter.es'))
            ->get(route('courses.show', $course))->assertNotFound();
    }

    public function test_inactive_course_is_visible_to_admin_and_enrolled_students(): void
    {
        $course = Course::create(['name' => 'Oculto', 'code' => 'OC-1', 'status' => 'inactive']);
        $student = $this->user(Role::STUDENT, 'maria@educenter.es');
        Enrollment::create(['student_id' => $student->id, 'course_id' => $course->id, 'status' => 'cancelled']);

        $this->actingAs($this->admin())->get(route('courses.show', $course))->assertOk();
        $this->actingAs($student)->get(route('courses.show', $course))->assertOk();
    }

    // --- Rate limits ------------------------------------------------------------

    public function test_register_limit_is_per_email_so_classmates_are_not_blocked(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', ['email' => 'a@educenter.es', 'dni' => 'X', 'password' => 'x']);
        }

        $this->post('/register', ['email' => 'a@educenter.es', 'dni' => 'X', 'password' => 'x'])->assertStatus(429);
        // Same IP, another student
        $this->post('/register', ['email' => 'b@educenter.es', 'dni' => 'X', 'password' => 'x'])->assertRedirect();
    }

    public function test_login_and_register_have_per_ip_limits(): void
    {
        $request = Request::create('/login', 'POST', ['email' => 'a@educenter.es'], server: ['REMOTE_ADDR' => '10.0.0.1']);

        $login = RateLimiter::limiter('login')($request);
        $register = RateLimiter::limiter('register')($request);

        $this->assertSame(60, $login[0]->maxAttempts);
        $this->assertSame('login-ip:10.0.0.1', $login[0]->key);
        $this->assertSame(30, $register[1]->maxAttempts);
        $this->assertSame('register-ip:10.0.0.1', $register[1]->key);
    }

    // --- Seeder ---------------------------------------------------------------------

    public function test_demo_seeder_refuses_to_run_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->expectException(RuntimeException::class);

        try {
            $this->app->call([new DatabaseSeeder, 'run']);
        } finally {
            $this->assertSame(0, User::count());
        }
    }
}
