@extends('layouts.public')

@section('title', 'Cursos - ZiberEibar')

@section('content')
<section class="container page-top">
    <div class="section-head">
        <div class="section-head__title">
            <span class="kicker fx-fade">~/cursos</span>
            <h1 class="h-page"><span class="fx-type">Cursos abiertos.</span></h1>
        </div>
        <p class="fx-fade" style="--d: 0.6s">{{ $courses->count() }} {{ $courses->count() === 1 ? 'curso disponible' : 'cursos disponibles' }}. Para matricularte necesitas tu cuenta de alumno activada.</p>
    </div>

    @if ($courses->isNotEmpty())
        @include('partials.course-browser')
    @else
        <div class="panel empty">
            <span class="empty__cmd">ls cursos/ <span class="muted">— vacío</span></span>
            <span>Todavía no hay cursos abiertos. Vuelve pronto.</span>
        </div>
    @endif
</section>
@endsection
