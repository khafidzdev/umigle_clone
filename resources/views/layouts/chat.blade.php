<!DOCTYPE html>
<html class="no-js" lang="">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>YuhChat - Chat</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}" />

    <!-- ======== CSS here ======== -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/lineicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
            overflow: hidden; /* Prevent scrolling, true OmeTV feel */
        }
        .top-bar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-icon {
            background-color: #5864FF;
            color: white;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #5864FF;
            margin: 0;
        }
        .main-container {
            height: calc(100vh - 60px); width: 100%;
            display: flex;
            padding: 15px;
            gap: 15px;
        }
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="logo-container">
            <a href="{{ url('/') }}" style="text-decoration: none;">
                <h1 style="color: #5864FF; font-weight: bold; margin: 0; font-size: 36px; letter-spacing: -1px;">YuhChat</h1>
            </a>
        </div>

    </div>

    @yield('content')

    <!-- ======== JS here ======== -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
  </body>
</html>
