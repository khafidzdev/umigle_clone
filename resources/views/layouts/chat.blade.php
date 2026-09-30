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
            height: calc(100dvh - 60px);
            width: 100%;
            display: flex;
            padding: 15px;
            gap: 15px;
        }
        .col-video {
            flex: 7;
            position: relative; /* For absolute positioning of local video */
            height: 100%;
            overflow: hidden;
            border-radius: 8px;
        }
        .col-chat {
            flex: 3;
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            height: 100%;
        }

        .remote-video-container {
            width: 100%;
            height: 100%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #444 !important;
        }
        .local-video-container {
            position: absolute;
            bottom: 20px;
            left: 20px;
            width: 20%;
            aspect-ratio: 4/3;
            overflow: hidden;
            background-color: #333;
            border: 2px solid white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            z-index: 10;
        }
        
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                padding: 10px;
                gap: 10px;
            }
            .col-video {
                flex: 0 0 60%;
            }
            .col-chat {
                flex: 1;
            }
            .local-video-container {
                width: 80px;
                height: 112px;
                bottom: 10px;
                left: 10px;
            }
            .top-bar {
                height: 50px;
                padding: 0 10px;
            }
            .top-bar h1 {
                font-size: 24px !important;
            }
            .main-container {
                height: calc(100dvh - 50px);
            }
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
