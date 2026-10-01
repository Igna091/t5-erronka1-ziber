{{-- Activation status of a pending student or teacher. Needs $user, $activationInfo and $resendUrl. --}}
@php
    $sentAt = $activationInfo['sentAt'];
    $expired = $activationInfo['expired'];
@endphp
<section class="panel panel--warn fx-fade" style="--d: 0.3s">
    <div class="panel__head"><h2 class="lbl">{{ __('activación de la cuenta') }}</h2><span class="xs warn">{{ __('pendiente') }}</span></div>
    <div class="panel__body">
        <ol class="steps">
            <li class="is-done">{{ __('cuenta creada por administración · :date', ['date' => $user->created_at->format('d.m.Y')]) }}</li>
            @if ($sentAt && !$expired)
                <li class="is-done">{{ __('email de activación enviado · :when', ['when' => $sentAt->diffForHumans()]) }}</li>
            @elseif ($sentAt)
                <li class="is-wait">{{ __('el enlace caducó el :date', ['date' => $activationInfo['expiresAt']->format('d.m.Y')]) }}</li>
            @else
                <li class="is-wait">{{ __('no hay ningún enlace de activación vigente') }}</li>
            @endif
            <li class="is-wait">{{ __(':name elige su contraseña con el enlace', ['name' => $user->name]) }}</li>
        </ol>
        <p class="small text-2" style="line-height: 1.7;">{{ __('El enlace caduca a los 7 días y solo sirve una vez. Si lo ha perdido o ha caducado, envía uno nuevo: el anterior dejará de funcionar.') }}</p>
        <form method="POST" action="{{ $resendUrl }}" class="form-actions">
            @csrf
            <button type="submit" class="btn btn-primary" @if ($activationInfo['cooldown'] > 0) data-countdown="{{ $activationInfo['cooldown'] }}" @endif>{{ __('Reenviar email de activación') }}</button>
            <span class="xs muted">{{ __('máximo un envío por minuto') }}</span>
        </form>
    </div>
</section>
