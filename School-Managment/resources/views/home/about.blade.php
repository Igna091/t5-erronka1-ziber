@extends('layouts.public')

@section('title', 'Información - EduCenter')

@section('content')
<section class="section">
    <div class="info-section">
        <h2>Sobre EduCenter</h2>
        <p>EduCenter es un centro de formación profesional comprometido con la excelencia educativa. Desde nuestra fundación, nos hemos dedicado a ofrecer programas formativos de alta calidad que preparan a nuestros alumnos para enfrentar los retos del mercado laboral actual.</p>
        <p>Contamos con un equipo docente altamente cualificado y unas instalaciones modernas que facilitan un aprendizaje práctico y efectivo.</p>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon" style="background:rgba(30,58,138,0.1);color:var(--primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a23.838 23.838 0 0 0-1.012 5.434c0 .043.016.086.044.124a23.77 23.77 0 0 0 4.454-1.601M4.26 10.147A23.96 23.96 0 0 1 12 8.443a23.96 23.96 0 0 1 7.74 1.704M4.26 10.147 12 6l7.74 4.147M12 6V3"/></svg>
                </div>
                <h4>Formación de calidad</h4>
                <p>Programas actualizados y orientados al mercado laboral con metodología práctica.</p>
            </div>
            <div class="info-card">
                <div class="info-card-icon" style="background:rgba(5,150,105,0.1);color:var(--tertiary);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                </div>
                <h4>Profesorado experto</h4>
                <p>Docentes con amplia experiencia profesional y vocación educativa.</p>
            </div>
            <div class="info-card">
                <div class="info-card-icon" style="background:rgba(37,99,235,0.1);color:var(--secondary);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </div>
                <h4>Instalaciones modernas</h4>
                <p>Espacios equipados con la última tecnología para un aprendizaje óptimo.</p>
            </div>
        </div>

        <h2>Nuestra misión</h2>
        <p>Formar profesionales competentes y comprometidos, capaces de adaptarse a un entorno laboral en constante evolución, mediante una educación de calidad, innovadora y accesible.</p>

        <h2>Contacto</h2>
        <p>¿Tienes alguna pregunta? No dudes en contactarnos:</p>
        <p><strong>Email:</strong> info@educenter.es<br>
           <strong>Teléfono:</strong> 900 000 000<br>
           <strong>Dirección:</strong> Calle Educación, 1</p>
    </div>
</section>
@endsection
