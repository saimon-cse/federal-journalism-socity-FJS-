@php
    use App\Models\Setting;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ Setting::get('site_name', config('app.name')) }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset(Setting::get('favicon_image', 'backend/assets/images/default-favicon.png')) }}" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles -->
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
    {{-- Ensure financial-modules.css is distinct and loaded if needed, or combined into style.css --}}
    @if(file_exists(public_path('backend/css/financial-modules.css')))
        <link href="{{ asset('backend/css/financial-modules.css') }}" rel="stylesheet">
    @endif

    <!-- Bootstrap CSS -->
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}


    {{-- @stack('styles') --}}
    @yield('styles')
</head>
<body class="{{-- Will be populated by JS, e.g., 'sidebar-open' --}}">
    <div class="admin-container">
        <!-- Sidebar -->
        @include('layouts.admin.partials.sidebar')

        <!-- Main Content Container -->
        <div class="content-container">
            <!-- Navbar -->
            @include('layouts.admin.partials.navbar')

            <!-- Main Content -->
            <main class="main-content">
                <div class="header">
                    <div class="header-left">
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="header-right">
                        @yield('header-actions')
                    </div>
                </div>

                {{-- Session Messages & Errors --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle alert-icon"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle alert-icon"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-ban alert-icon"></i>
                        <div>
                            <p class="font-weight-bold mb-1">Please correct the following errors:</p>
                            <ul class="mb-0 pl-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Sidebar backdrop for mobile --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

    <script src="{{ asset('backend/js/script.js') }}"></script>
    {{-- TinyMCE will be pushed via @stack('scripts') on specific pages --}}

    {{-- @stack('scripts') --}}
    @yield('scripts')
</body>
</html>
