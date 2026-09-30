<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            ziber_eibar
        </x-mail::header>
    </x-slot:header>

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        <x-slot:subcopy>
            <x-mail::subcopy>
                {{ $subcopy }}
            </x-mail::subcopy>
        </x-slot:subcopy>
    @endisset

    {{-- Footer: the same as the HTML version --}}
    <x-slot:footer>
        <x-mail::footer>
            {{ __('Centro de formación profesional · Eibar, Gipuzkoa') }} · info@zibereibar.eus · © {{ date('Y') }} ZiberEibar
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
