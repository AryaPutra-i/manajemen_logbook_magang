@php
    $path = '';
@endphp

@if (!empty($pathSegments))
    <div aria-label="breadcrumb" class="border-top border-black align-self-start px-3 py-2 w-100"
        style="background-color: #DDA15E;">
        <ol class="breadcrumb my-auto">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i
                        class="bi bi-house-door text-primary me-1"></i>Home</a></li>
            @foreach ($pathSegments as $index => $pathSegment)
                @php
                    $path .= "/$pathSegment";
                    $isLast = $index + 1 === count($pathSegments);
                @endphp

                @if (!$isLast)
                    <li class="breadcrumb-item"><a
                            href="{{ url($path) }}">{{ ucwords(str_replace('-', ' ', $pathSegment)) }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ ucwords(str_replace('-', ' ', $pathSegment)) }}
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
@endif
