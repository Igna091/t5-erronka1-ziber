{{--
    List of courses + detail panel of the selected one.
    Needs: $courses (with enrollments_count of active enrollments), $enrolledIds.
    Rows are links to the course page; app.js turns them into a selector on wide screens.
--}}
<div class="browser" data-browser>
    <div class="ls ls--courses">
        <div class="ls__head lbl" aria-hidden="true">
            <span></span><span>código</span><span>curso</span><span>horas</span><span class="col-meter">plazas</span><span class="col-free">libres</span>
        </div>
        @foreach ($courses as $course)
            <a href="{{ route('courses.show', $course) }}"
               class="ls__row {{ $loop->first ? 'is-sel' : '' }}"
               data-course-row
               data-target="course-panel-{{ $course->id }}"
               aria-current="{{ $loop->first ? 'true' : 'false' }}">
                <span class="ls__mark" aria-hidden="true">{{ $loop->first ? '>' : '' }}</span>
                <span class="ls__code">{{ $course->code }}</span>
                <span class="ls__name">{{ $course->slug }}</span>
                <span>{{ $course->duration_hours ? $course->duration_hours.'h' : '—' }}</span>
                <span class="ascii col-meter" aria-hidden="true">[{{ $course->seatMeter() }}]</span>
                <span class="col-free">
                    @if ($course->capacity)
                        <span class="sr-only">plazas libres:</span>{{ $course->available_spots }}/{{ $course->capacity }}
                    @else
                        libre
                    @endif
                </span>
            </a>
        @endforeach
    </div>

    <div>
        @foreach ($courses as $course)
            @php($enrolled = in_array($course->id, $enrolledIds, true))
            <article class="panel panel--float course-panel" id="course-panel-{{ $course->id }}" @unless($loop->first) hidden @endunless aria-labelledby="course-panel-title-{{ $course->id }}">
                <x-pads />
                <div class="panel__row"><span class="lbl">ficha del curso</span><span class="lbl">{{ $course->code }}</span></div>
                <h3 class="h-panel" id="course-panel-title-{{ $course->id }}">{{ $course->name }}</h3>
                <p class="text-2 small" style="line-height: 1.75;">{{ $course->description ?? 'Sin descripción disponible.' }}</p>

                <div class="specs specs--plain" style="--cols: 2;">
                    <div class="spec"><span class="lbl">duración</span><span class="spec__value">{{ $course->duration_hours ? $course->duration_hours.' h' : '—' }}</span></div>
                    <div class="spec"><span class="lbl">plazas libres</span><span class="spec__value">{{ $course->capacity ? $course->available_spots.' de '.$course->capacity : 'sin límite' }}</span></div>
                    <div class="spec"><span class="lbl">inicio</span><span class="spec__value">{{ $course->start_date?->format('d.m.Y') ?? '—' }}</span></div>
                    <div class="spec"><span class="lbl">fin</span><span class="spec__value">{{ $course->end_date?->format('d.m.Y') ?? '—' }}</span></div>
                </div>

                @if ($course->capacity && $course->capacity <= 40)
                    <div class="seats" aria-hidden="true">
                        @for ($i = 0; $i < $course->capacity; $i++)
                            <i class="seat {{ $i < $course->available_spots ? 'is-free' : '' }}" style="--d: {{ number_format(0.02 * $i, 2) }}s"></i>
                        @endfor
                    </div>
                @endif

                <div class="form-actions">
                    @auth
                        @if ($enrolled)
                            <span class="status status--pill" style="color: var(--acc);">matriculado/a</span>
                        @elseif (auth()->user()->isStudent() && $course->hasAvailableSpots())
                            <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Matricularme</button>
                            </form>
                        @elseif (auth()->user()->isStudent())
                            <span class="status status--off">sin plazas libres</span>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Inicia sesión</a>
                    @endauth
                    <a href="{{ route('courses.show', $course) }}" class="link-arrow">ver ficha completa</a>
                </div>
            </article>
        @endforeach
    </div>
</div>
