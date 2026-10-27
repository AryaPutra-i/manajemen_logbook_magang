<x-base-layout>
    <x-desktop-side-navbar></x-desktop-side-navbar>


    <main class="vh-100 overflow-y-auto flex-grow-1 d-flex flex-column align-items-center px-0">
        <x-mobile-navbar></x-mobile-navbar>
        {{ $slot }}
    </main>
</x-base-layout>
