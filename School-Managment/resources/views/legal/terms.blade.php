@extends('layouts.public')

@section('title', __('Términos y condiciones - ZiberEibar'))

@section('content')
{{-- The text is long, so there is one view per language (legal/{es,eu,en}/). Text in <span class="ph"> is a placeholder: the centre has to fill it in --}}
<article class="container page-top legal">
    <span class="kicker fx-fade">// {{ __('legal') }}</span>
    @includeFirst(['legal.'.app()->getLocale().'.terms', 'legal.es.terms'])

    <div class="form-actions" style="margin-top: 1rem;">
        <a href="{{ route('legal.privacy') }}" class="link-arrow">{{ __('política de privacidad') }}</a>
    </div>
</article>
@endsection
