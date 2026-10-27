<div class="position-sticky z-3 left-0 vh-100 d-flex flex-column d-none d-lg-flex p-0 border-end"
    style="border-right-color: #283618 !important; background-color: #283618; min-width: 384px !important;">
    <a href="{{ route('home') }}"
        class="px-3 py-4 d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <img class="me-2" src="{{ asset('images/ehealth_bg-removed.png') }}" style="width: 32px; height: 32px;"
            alt="">
        <span style="color: #FEFAE0;" class="fs-6 fw-bold">Web Logbook Dokter Magang</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li style="@if (request()->routeIs('home')) background-color: #FEFAE0; color: #283618; @endif"
            class="nav-item">
            <a href="{{ route('home') }}"
                style="@if (request()->routeIs('home')) color: #283618; @else color: #FEFAE0; @endif"
                class="align-items-center d-flex gap-2 fs-6 nav-link text-decoration-none" aria-current="page">
                <i style="@if (request()->routeIs('home')) color: #283618; @else color: #FEFAE0; @endif"
                    class="fs-3 me-2 bi bi-house-door" width="16" height="16" aria-hidden="true"></i>
                Beranda
            </a>
        </li>

        @foreach ($navLinks as $navLink)
            <li style="@if (request()->url() === $navLink['href']) background-color: #FEFAE0; @endif">
                <a href="{{ $navLink['href'] }}"
                    style="@if (request()->url() === $navLink['href']) color: #283618; @else color: #FEFAE0; @endif"
                    @class([
                        'align-items-center d-flex gap-2 fs-6 nav-link text-decoration-none',
                        'ps-5' => $navLink['level'] === 2,
                    ]) @if (request()->url() === $navLink['href']) aria-current="page" @endif
                    href="{{ $navLink['href'] }}">
                    <i style="@if (request()->url() === $navLink['href']) color: #283618; @else color: #FEFAE0; @endif"
                        class="fs-3 me-2 bi {{ $navLink['icon'] }}" width="16" height="16"
                        aria-hidden="true"></i>
                    {{ $navLink['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
    <hr>
    <form class="nav-item" action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="d-flex align-items-center gap-3 btn w-100 text-start fs-6"
            style="color: #FEFAE0;"><i class="bi bi-box-arrow-left fs-3" style="color: #FEFAE0;"></i>
            Logout</button>
    </form>
</div>
