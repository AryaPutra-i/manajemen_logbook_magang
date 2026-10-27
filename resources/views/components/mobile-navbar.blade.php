<div class="position-sticky z-3 top-0 w-100">
    <nav class="container-fluid navbar navbar-expand-lg bg-body-tertiary w-100 py-0"
        style="background-color: #BC6C25 !important; min-height: 80px;">
        <div class="w-100 h-100 d-flex align-items-center justify-content-between p-3">
            <h1 style="color: #FEFAE0;" class="my-auto text-break d-flex align-items-center fs-1 gap-2 fw-bold p-0">
                @if ($lastSegment == '')
                    Home
                @else
                    <a href="{{ $backUrl }}" aria-label="Kembali"><i class="bi bi-arrow-left"
                            style="color: #FEFAE0;"></i></a>
                    {{ $lastSegment }}
                @endif
            </h1>
            <button style="width: 48px; height: 48px; border-color: #FEFAE0; color: #FEFAE0;"
                class="d-lg-none navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="w-100 h-100 navbar-toggler-icon"
                    style="filter: brightness(0) saturate(100%) invert(91%) sepia(2%) saturate(2077%) hue-rotate(355deg) brightness(108%) contrast(99%);"></span>
            </button>
        </div>
        <div class="d-lg-none collapse navbar-collapse" style="background-color: #DDA15E" id="navbarSupportedContent">
            <ul class="d-lg-none navbar-nav me-auto mb-0">
                <li class="nav-item px-3" style="@if (request()->routeIs('home')) background-color: #FEFAE0; @endif">
                    <a class='nav-link' @if (request()->routeIs('home')) aria-current="page" @endif
                        href="{{ route('home') }}">Home</a>
                </li>
                @foreach ($navLinks as $navLink)
                    <li class="nav-item px-3"
                        style="@if (request()->url() === $navLink['href']) background-color: #FEFAE0; @endif">
                        <a @class(['nav-link', 'ps-2' => $navLink['level'] === 2]) @if (request()->url() === $navLink['href']) aria-current="page" @endif
                            href="{{ $navLink['href'] }}">{{ $navLink['label'] }}</a>
                    </li>
                @endforeach
                <form class="nav-item px-3 pt-2 pb-2" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-100 text-start"
                        style="background: none; border: none; padding: 0; cursor: pointer;"><i
                            class="bi bi-box-arrow-left"></i> Logout</button>
                </form>
            </ul>
        </div>
    </nav>
    <x-breadcrumb />
</div>
