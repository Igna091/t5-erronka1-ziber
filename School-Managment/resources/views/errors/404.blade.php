@extends('errors.layout')

@section('code', '404')
@section('title', 'Página no encontrada')
@section('message', 'La página que buscas no existe o se ha movido.')
@section('actions')<a href="{{ route('courses.index') }}" class="link-cmd">ver cursos</a>@endsection
