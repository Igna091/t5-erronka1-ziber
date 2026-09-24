@extends('errors.layout')

@section('code', '403')
@section('title', 'Acceso denegado')
@section('message', $exception->getMessage() ?: 'No tienes permiso para ver esta página.')
@section('actions')<a href="{{ route('login') }}" class="link-cmd">iniciar sesión con otra cuenta</a>@endsection
