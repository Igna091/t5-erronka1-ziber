@extends('layouts.public')

@section('title', 'Mis matrículas - ZiberEibar')

@section('content')
<div class="student-page">
    <div class="student-page-header">
        <h1>Mis matrículas</h1>
        <p>Cursos en los que estás matriculado.</p>
    </div>

    @if($enrollments->count() > 0)
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Código</th>
                        <th>Fecha de matrícula</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                        <tr>
                            <td>
                                <a href="{{ route('courses.show', $enrollment->course) }}" style="font-weight:500;color:var(--text-primary);">
                                    {{ $enrollment->course->name }}
                                </a>
                            </td>
                            <td><span class="course-card-code">{{ $enrollment->course->code }}</span></td>
                            <td>{{ $enrollment->enrolled_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($enrollment->status === 'active')
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge badge-danger">Cancelada</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>
                <h3>Sin matrículas</h3>
                <p>Aún no te has matriculado en ningún curso.</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary btn-sm">Ver cursos disponibles</a>
            </div>
        </div>
    @endif
</div>
@endsection
