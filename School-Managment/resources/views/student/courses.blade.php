@extends('layouts.public')

@section('title', __('Cursos - ZiberEibar'))

@section('content')
<section class="container page-top">
    <div class="section-head">
        <div class="section-head__title">
            <span class="kicker fx-fade">// {{ __('cursos') }}</span>
            <h1 class="h-page"><span class="fx-type">{{ __('Cursos abiertos.') }}</span></h1>
        </div>
        <p class="fx-fade" style="--d: 0.6s">{{ trans_choice(':count curso disponible.|:count cursos disponibles.', $courses->count()) }} {{ __('Para matricularte necesitas tu cuenta de alumno activada.') }}</p>
    </div>

    @if ($courses->isNotEmpty())
        @include('partials.course-browser')
    @else
        <div class="panel empty">
            <span class="empty__cmd">ls {{ __('cursos') }}/ <span class="muted">— {{ __('vacío') }}</span></span>
            <span>{{ __('Todavía no hay cursos abiertos. Vuelve pronto.') }}</span>
        </div>
    @endif
</section>
@endsection
