@extends('layouts.public')

@section('title', __('ZiberEibar - Centro de formación profesional'))

@section('content')
<section class="hero">
    <div class="container hero__inner">
        <div class="hero__copy">
            <div class="kicker fx-fade" style="color: var(--muted);">
                <span class="led" aria-hidden="true"></span>
                {{ __('Matrícula abierta') }}{{ $academicYear ? ' · '.__('Curso :year', ['year' => $academicYear]) : '' }}
            </div>
            <h1 class="h-hero">
                <span class="fx-type">{{ __('Construye.') }}</span>
                <span class="fx-type" style="--d: 0.8s">{{ __('Administra.') }}</span>
                <span class="fx-type acc" style="--d: 1.6s">{{ __('Protege.') }}<span class="cursor" aria-hidden="true"></span></span>
            </h1>
            <p class="lead fx-fade" style="--d: 2.2s">{{ __('Formación profesional en Eibar: desarrollo web, diseño UX/UI, sistemas, ciberseguridad e inteligencia artificial. Prácticas reales, plazas limitadas y matrícula online.') }}</p>
            <div class="hero__cta fx-fade" style="--d: 2.4s">
                <a href="#cursos" class="btn btn-primary">{{ __('Ver cursos') }} <x-icon name="arrow-right" :size="20" /></a>
                <a href="{{ route('about') }}#matricula" class="link-cmd">{{ __('cómo matricularse') }}</a>
            </div>
        </div>

        <div class="hero__side">
            <div class="terminal fx-fade" style="--d: 0.2s">
                <div class="terminal__bar"><span>{{ __('zibereibar@eibar: ~/matricula') }}</span><span>tty1</span></div>
                <p class="sr-only">{{ trans_choice(':count curso abierto. La lista completa está más abajo.|:count cursos abiertos. La lista completa está más abajo.', $courses->count()) }}</p>
                <div class="terminal__body" aria-hidden="true">
                    <div class="tl fx-tl" style="--d: 0.3s"><span class="prompt">$</span> {{ __('ziber cursos --abiertos') }}</div>
                    @forelse ($courses->take(6) as $course)
                        <div class="tl tl--dim fx-tl" style="--d: {{ number_format(0.9 + $loop->index * 0.15, 2) }}s">{{ mb_str_pad($course->code, 8) }}{{ mb_str_pad(\Illuminate\Support\Str::limit($course->slug, 25, '…'), 28) }}{{ $course->capacity ? $course->available_spots.'/'.$course->capacity : __('libre') }}</div>
                    @empty
                        <div class="tl tl--dim fx-tl" style="--d: 0.9s">{{ __('sin cursos abiertos por ahora') }}</div>
                    @endforelse
                    <div class="tl fx-tl" style="--d: 2s"><span class="prompt">$</span> {{ __('ziber ayuda matricula') }}</div>
                    <div class="tl tl--ok fx-tl" style="--d: 2.5s">&gt; {{ __('entra con tu cuenta y pulsa «Matricularme»') }}</div>
                    <div class="tl fx-tl" style="--d: 2.9s"><span class="prompt">$</span> <span class="cursor cursor--text"></span></div>
                </div>
            </div>
            {{-- Tall viewBox + "slice": keeps ~1:1 scale and the lines run past the bottom edge --}}
            <svg class="hero__wires" viewBox="0 0 576 600" preserveAspectRatio="xMinYMin slice" fill="none" aria-hidden="true">
                <path class="trace" style="--d: 2.6s; stroke: var(--line-3);" d="M160 0 V60 L200 100 V600" pathLength="100" stroke-width="2"/>
                <path class="trace" style="--d: 2.75s; stroke: var(--line-3);" d="M200 0 V40 L240 80 V600" pathLength="100" stroke-width="2"/>
                <path class="trace" style="--d: 2.9s; stroke: var(--line-3);" d="M240 0 V20 L280 60 V600" pathLength="100" stroke-width="2"/>
                <path class="pulse" style="--d: 4s; stroke: var(--acc);" d="M160 0 V60 L200 100 V600" pathLength="100" stroke-width="3" stroke-linecap="round"/>
                <path class="pulse" style="--d: 4.6s; stroke: var(--acc);" d="M200 0 V40 L240 80 V600" pathLength="100" stroke-width="3" stroke-linecap="round"/>
                <path class="pulse" style="--d: 5.2s; stroke: var(--acc);" d="M240 0 V20 L280 60 V600" pathLength="100" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
</section>

<section class="section container" id="cursos">
    <div class="section-head">
        <div class="section-head__title">
            <span class="kicker">02 // {{ __('cursos') }}</span>
            <h2 class="h-section">{{ __('Elige un módulo.') }}</h2>
        </div>
        <p>{{ __('Selecciona un curso para ver su ficha. Para matricularte necesitas tu cuenta de alumno activada.') }}</p>
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
