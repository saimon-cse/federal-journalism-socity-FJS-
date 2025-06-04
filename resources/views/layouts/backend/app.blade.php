<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('title', config('app.name'))</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    @include('layouts.backend.partials.headerfile')
      <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>


    {{-- Aditional Styles --}}
    @yield('styles')

</head>

<body>

    <!-- ======= Header ======= -->
    @include('layouts.backend.partials.navbar')
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    @include('layouts.backend.partials.sidebar')
    <!-- End Sidebar-->

    <main id="main" class="main">

        @yield('content')

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
        </div>
        <div class="credits">

            Designed by <a href="#">BootstrapMade</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

            <!-- jQuery CDN (required for AJAX and DOM manipulation) -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            @include('layouts.backend.partials.tinymce')
            @include('layouts.backend.partials.footerfile')
            @yield('scripts')

</body>

</html>
