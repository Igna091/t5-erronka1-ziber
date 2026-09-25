@extends('layouts.public')

@section('title', 'Términos y condiciones - ZiberEibar')

@section('content')
{{-- Text in <span class="ph"> is a placeholder: the centre has to fill it in --}}
<article class="container page-top legal">
    <span class="kicker fx-fade">// legal</span>
    <h1 class="h-page"><span class="fx-type">Términos y condiciones.</span></h1>
    <p class="muted small">Última actualización: 25 de septiembre de 2026</p>

    <h2>1. Titular del sitio web</h2>
    <p>En cumplimiento del artículo 10 de la Ley 34/2002, de servicios de la sociedad de la información y de comercio electrónico (LSSI-CE), te informamos de que este sitio web pertenece a ZiberEibar, con CIF <span class="ph">[CIF]</span> y domicilio en Calle Educación, 1, 20600 Eibar (Gipuzkoa). Contacto: <a href="mailto:info@zibereibar.eus">info@zibereibar.eus</a> · 900 000 000.</p>

    <h2>2. Objeto y aceptación</h2>
    <p>Estos términos regulan el uso de la web de ZiberEibar y de las cuentas de alumno para consultar cursos y matricularse en ellos. Al usar la web o activar tu cuenta aceptas estas condiciones y nuestra <a href="{{ route('legal.privacy') }}">política de privacidad</a>.</p>

    <h2>3. Tu cuenta</h2>
    <p>El centro crea tu cuenta y te envía un enlace para activarla y elegir tu contraseña. La contraseña es personal: no la compartas con nadie. Si crees que alguien ha accedido a tu cuenta, avisa a administración.</p>

    <h2>4. Matrículas</h2>
    <p>Las matrículas están sujetas a las plazas disponibles de cada curso. Solo administración puede cancelar una matrícula; si necesitas hacerlo, contacta con el centro.</p>

    <h2>5. Uso aceptable</h2>
    <p>Al usar la plataforma te comprometes a:</p>
    <ul>
        <li>No compartir tu cuenta ni usar la de otra persona.</li>
        <li>No intentar acceder a datos de otros alumnos ni a zonas para las que no tienes permiso.</li>
        <li>No interferir en el funcionamiento de la web ni intentar saltarse sus medidas de seguridad.</li>
        <li>No usar la plataforma para fines ilícitos o contrarios a estos términos.</li>
    </ul>
    <p>El incumplimiento de estas normas puede suponer la suspensión de tu cuenta.</p>

    <h2>6. Propiedad intelectual</h2>
    <p>Los contenidos de esta web (textos, imágenes, diseño y logotipo) pertenecen a ZiberEibar y están protegidos por la legislación de propiedad intelectual. Puedes reutilizar los contenidos formativos citando siempre a ZiberEibar como autor y la dirección de la web como fuente. El nombre, el logotipo y la marca ZiberEibar no pueden usarse sin autorización escrita. Para cualquier otro uso, escribe a <a href="mailto:info@zibereibar.eus">info@zibereibar.eus</a>.</p>

    <h2>7. Responsabilidad</h2>
    <p>ZiberEibar procura que la información de la web sea actual y correcta, pero no se hace responsable:</p>
    <ul>
        <li>Del uso indebido que los usuarios hagan de los contenidos o de la plataforma.</li>
        <li>De los errores técnicos, interrupciones o suspensiones del servicio por causas técnicas, fortuitas o de fuerza mayor que no le sean atribuibles.</li>
        <li>De las opiniones que terceros puedan expresar en los contenidos enlazados desde la web.</li>
    </ul>

    <h2>8. Ley aplicable</h2>
    <p>Estas condiciones se rigen por la legislación española, en particular la Ley 34/2002 (LSSI-CE), el Reglamento General de Protección de Datos (UE 2016/679), la Ley Orgánica 3/2018 (LOPDGDD) y la normativa de protección de consumidores y usuarios.</p>

    <h2>9. Más información</h2>
    <p>Para cualquier duda, sugerencia o solicitud puedes escribir a <a href="mailto:info@zibereibar.eus">info@zibereibar.eus</a>, llamar al 900 000 000 o dirigirte a Calle Educación, 1, 20600 Eibar (Gipuzkoa).</p>

    <div class="form-actions" style="margin-top: 1rem;">
        <a href="{{ route('legal.privacy') }}" class="link-arrow">política de privacidad</a>
    </div>
</article>
@endsection
