@extends('errors.layout')

@section('code', '404')
@section('title', __('Página no encontrada'))
@section('message', __('La página que buscas no existe o se ha movido.'))
@section('actions')<a href="{{ route('courses.index') }}" class="link-cmd">{{ __('ver cursos') }}</a>@endsection
