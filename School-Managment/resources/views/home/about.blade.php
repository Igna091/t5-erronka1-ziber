@extends('layouts.public')

@section('title', 'Información - ZiberEibar')

@section('content')
<section class="container page-top">
    <div class="section-head">
        <div class="section-head__title">
            <span class="kicker fx-fade">// información</span>
            <h1 class="h-page"><span class="fx-type">Sobre</span><span class="fx-type acc" style="--d: 0.6s">ZiberEibar.<span class="cursor" aria-hidden="true"></span></span></h1>
        </div>
        <div class="stack fx-fade" style="--d: 1s; max-width: 34rem;">
            <p class="lead">ZiberEibar es un centro de formación profesional comprometido con la excelencia educativa. Nos dedicamos a ofrecer programas formativos de alta calidad que preparan a nuestros alumnos para el mundo laboral.</p>
            <p class="lead">Contamos con un equipo docente altamente cualificado y unas instalaciones modernas que facilitan un aprendizaje práctico y efectivo.</p>
        </div>
    </div>

    <div class="values">
        <article class="panel value fx-fade" style="--d: 1.1s">
            <span class="value__n">01</span>
            <h2>Formación de calidad</h2>
            <p>Programas actualizados y orientados al mercado laboral con metodología práctica.</p>
        </article>
        <article class="panel value fx-fade" style="--d: 1.2s">
            <span class="value__n">02</span>
            <h2>Profesorado experto</h2>
            <p>Docentes con amplia experiencia profesional y vocación educativa.</p>
        </article>
        <article class="panel value fx-fade" style="--d: 1.3s">
            <span class="value__n">03</span>
            <h2>Instalaciones modernas</h2>
            <p>Espacios equipados con la última tecnología para un aprendizaje óptimo.</p>
        </article>
    </div>
</section>

<section class="container section" id="matricula">
    <div class="section-head">
        <div class="section-head__title">
            <span class="kicker">// matrícula</span>
            <h2 class="h-section">Cómo matricularse.</h2>
        </div>
        <p>La matrícula es online. Solo necesitas tu cuenta de alumno.</p>
    </div>

    <div class="grid-2">
        <div class="terminal">
            <div class="terminal__bar"><span>zibereibar@eibar: ~/ayuda</span><span>tty1</span></div>
            <ol class="terminal__body steps" style="font-size: 0.9375rem; gap: 1rem;">
                <li class="is-done">El centro te da de alta con tu email.</li>
                <li class="is-done">Recibes un email con el enlace de activación (caduca en 7 días).</li>
                <li class="is-now">Abres el enlace y eliges tu contraseña.</li>
                <li>Inicias sesión y pulsas «Matricularme» en la ficha del curso.</li>
            </ol>
        </div>
        <div class="panel panel__pad">
            <x-pads :count="2" />
            <span class="lbl">¿no te llegó el email?</span>
            <p class="text-2" style="line-height: 1.7;">Revisa la carpeta de spam. Si el enlace ha caducado o lo has perdido, puedes pedir uno nuevo; el anterior dejará de funcionar.</p>
            <div class="form-actions">
                <a href="{{ route('register') }}" class="btn btn-outline btn-bracket">reenviar enlace</a>
                <a href="{{ route('courses.index') }}" class="link-arrow">ver cursos</a>
            </div>
        </div>
    </div>
</section>

<section class="container section">
    <div class="section-head">
        <div class="section-head__title">
            <span class="kicker">// misión</span>
            <h2 class="h-section">Nuestra misión.</h2>
        </div>
        <p>Formar profesionales competentes y comprometidos, capaces de adaptarse a un entorno laboral en constante evolución, mediante una educación de calidad, innovadora y accesible.</p>
    </div>

    <div class="panel contact-grid">
        <div class="spec"><span class="lbl">email</span><a class="spec__value" href="mailto:info@zibereibar.eus" style="font-size: 1.375rem;">info@zibereibar.eus</a></div>
        <div class="spec"><span class="lbl">teléfono</span><a class="spec__value" href="tel:+34900000000" style="font-size: 1.375rem; color: var(--text);">900 000 000</a></div>
        <div class="spec"><span class="lbl">dirección</span><span class="spec__value" style="font-size: 1.375rem;">Calle Educación, 1</span></div>
    </div>
</section>
@endsection
