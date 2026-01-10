<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Title --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Jetlouge Travels</title>

    {{-- styles|scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire styles --}}
    @livewireStyles

</head>
<body style="background-color: #f8f9fa !important;">

    {{-- header --}}
    @include('layouts.includes.header')

    {{-- Sidebar --}}
    @include('layouts.includes.sidebar')

    {{-- Overlay for mobile --}}
    <div id="overlay" @class('position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50') style="z-index:1040; display: none;"></div>

    {{-- Main --}}
    <main id="main-content">

        <!-- Page Header -->
        <div @class('page-header-container mb-4')>
            <div @class('d-flex justify-content-between align-items-center page-header')>
                <div @class('d-flex align-items-center')>
                    <div @class('dashboard-logo me-3')>
                        <img src="{{ asset('images/logo.png') }}" alt="Jetlouge Travels" @class('logo-img')>
                    </div>
                    <div>
                        <h2 @class('fw-bold mb-1')>@yield('page-title')</h2>
                        <p @class('text-muted mb-0')>@yield('page-subtitle')</p>
                    </div>
                </div>
                <nav aria-label="breadcrumb">
                    <ol @class('breadcrumb mb-0')>
                        <li @class('breadcrumb-item')>
                            <a href="{{ route('dashboard') }}" @class('text-decoration-none')>Home</a>
                        </li>
                        <li @class('breadcrumb-item active') aria-current="page">@yield('breadcrumbs')</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Page Body --}}
        {{ $slot }}

    </main>

    {{-- Trix Editor --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.js"></script>
    {{-- Livewire scripts --}}
    @livewireScripts
</body>
</html>