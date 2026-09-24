@extends('layouts.public')

@section('title', 'EduCenter - Centro de Formación Profesional')

@section('content')
{{-- Hero Section --}}
<section class="hero">
    <div class="hero-content">
        <h1>Formación que impulsa tu futuro</h1>
        <p>En EduCenter ofrecemos cursos profesionales de alta calidad diseñados para prepararte para el mundo laboral. Descubre tu potencial con nosotros.</p>
        <div class="hero-buttons">
            <a href="{{ route('courses.index') }}" class="btn btn-lg" style="background:white;color:var(--primary);border-color:white;">Ver cursos</a>
            <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg">Conoce nuestro centro</a>
        </div>
    </div>
</section>

{{-- Courses Section --}}
<section class="section" id="cursos">
    <div class="container">
        <div class="section-header">
            <h2>Cursos disponibles</h2>
            <p>Explora nuestra oferta formativa y encuentra el curso que mejor se adapte a tus objetivos profesionales.</p>
        </div>

        @if($courses->count() > 0)
            <div class="courses-grid">
                @foreach($courses as $course)
                    <div class="course-card">
                        <div class="course-card-header">
                            <span class="course-card-code">{{ $course->code }}</span>
                        </div>
                        <div class="course-card-body">
                            <h3>{{ $course->name }}</h3>
                            <p>{{ $course->description ?? 'Sin descripción disponible.' }}</p>
                        </div>
                        <div class="course-card-meta">
                            @if($course->duration_hours)
                                <span class="course-card-meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    {{ $course->duration_hours }}h
                                </span>
                            @endif
                            @if($course->capacity)
                                <span class="course-card-meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                    {{ $course->available_spots }}/{{ $course->capacity }} plazas
                                </span>
                            @endif
                            @if($course->start_date)
                                <span class="course-card-meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                    {{ $course->start_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                        <div class="course-card-footer">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline btn-sm">Ver curso</a>

                            @auth
                                @if(auth()->user()->isStudent())
                                    @php
                                        $isEnrolled = $course->enrollments->where('student_id', auth()->id())->where('status', 'active')->count() > 0;
                                    @endphp
                                    @if($isEnrolled)
                                        <span class="badge badge-success">Matriculado</span>
                                    @else
                                        <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Matricularme</button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm text-sm">Inicia sesión para matricularte</a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a23.838 23.838 0 0 0-1.012 5.434c0 .043.016.086.044.124a23.77 23.77 0 0 0 4.454-1.601M4.26 10.147A23.96 23.96 0 0 1 12 8.443a23.96 23.96 0 0 1 7.74 1.704M4.26 10.147 12 6l7.74 4.147M12 6V3"/></svg>
                <h3>No hay cursos disponibles</h3>
                <p>Próximamente añadiremos nuevos cursos. ¡Vuelve pronto!</p>
            </div>
        @endif
    </div>
</section>
@endsection
