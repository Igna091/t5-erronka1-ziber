@extends('errors.layout')

@section('code', '403')
@section('title', __('Acceso denegado'))
@section('message', $exception->getMessage() ?: __('No tienes permiso para ver esta página.'))
@section('actions')<a href="{{ route('login') }}" class="link-cmd">{{ __('iniciar sesión con otra cuenta') }}</a>@endsection
