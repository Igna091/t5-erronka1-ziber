@extends('errors.layout')

@section('code', '429')
@section('title', __('Demasiadas peticiones'))
@section('message', __('Has hecho demasiados intentos seguidos. Espera un minuto y vuelve a intentarlo.'))

