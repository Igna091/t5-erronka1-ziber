@extends('layouts.public')

@section('title', $course->name . ' - ZiberEibar')

@section('content')
<div class="student-page">
    <a href="{{ route('courses.index') }}" class="btn btn-ghost btn-sm mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        Volver a cursos
    </a>

    <div class="card" style="max-width:800px;">
        <div class="card-body" style="padding:2rem;">
            <span class="course-card-code mb-1" style="display:inline-block;">{{ $course->code }}</span>
            <h1 style="font-size:1.75rem;margin-bottom:1rem;">{{ $course->name }}</h1>

            <div class="course-card-meta mb-3" style="padding:0;">
                @if($course->duration_hours)
                    <span class="course-card-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ $course->duration_hours }} horas
                    </span>
                @endif
                @if($course->capacity)
                    <span class="course-card-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                        {{ $course->available_spots }} de {{ $course->capacity }} plazas disponibles
                    </span>
                @endif
                @if($course->start_date)
                    <span class="course-card-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        Inicio: {{ $course->start_date->format('d/m/Y') }}
                    </span>
                @endif
                @if($course->end_date)
                    <span class="course-card-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        Fin: {{ $course->end_date->format('d/m/Y') }}
                    </span>
                @endif
                <span class="course-card-meta-item">
                    @if($course->isActive())
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-neutral">Inactivo</span>
                    @endif
                </span>
            </div>

            <h3 style="font-size:1rem;margin-bottom:0.5rem;">Descripción</h3>
            <p style="color:var(--text-secondary);line-height:1.8;margin-bottom:1.5rem;">{{ $course->description ?? 'No hay descripción disponible para este curso.' }}</p>

            @auth
                @if(auth()->user()->isStudent())
                    @if($isEnrolled)
                        <div class="alert alert-success" style="margin-bottom:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Ya estás matriculado en este curso.
                        </div>
                    @elseif($course->isActive() && $course->hasAvailableSpots())
                        <form method="POST" action="{{ route('courses.enroll', $course) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">Matricularme en este curso</button>
                        </form>
                    @elseif(!$course->hasAvailableSpots())
                        <div class="alert alert-warning" style="margin-bottom:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                            No quedan plazas disponibles en este curso.
                        </div>
                    @endif
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Inicia sesión para matricularte</a>
            @endauth
        </div>
    </div>
</div>
@endsection
