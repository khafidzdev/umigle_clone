<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - YuhTv</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>

    <!-- Navbar sama persis -->
    <nav class="nav" role="navigation" aria-label="Main navigation">
        <div class="nav-inner">
            <a href="/" class="nav-logo" aria-label="YuhTv Home">
                <span class="nav-logo-mark">
                    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 8L7 4L13 8L7 12L3 8Z" fill="#111"/>
                    </svg>
                </span>
                YuhTv
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="/">Home</a></li>
            </ul>
        </div>
    </nav>

<section class="hero" style="min-height: 80vh; display: flex; align-items: center;">
        <div class="container">
            <!-- Kata class="reveal" sudah dihapus agar teksnya langsung muncul -->
            <h1>Welcome back to <span class="accent-word">YuhTv</span></h1>
            <p class="hero-sub" style="max-width: 600px; margin-left: auto; margin-right: auto;">
                Sign in to start matching with strangers and enjoy non-stop video chatting.
            </p>
            
            <div class="hero-actions" style="justify-content: center; margin-top: 2rem;">
                <a href="/auth/google" class="btn-acid" style="background-color: white; color: #111; gap: 12px; padding: 16px 24px; font-size: 18px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>
                    Sign in with Google
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Simple -->
    <footer class="footer" id="footer" style="margin-top: 0; padding-top: 4rem; border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="container">
            <div class="footer-bottom" style="border-top: none; padding-top: 0;">
                <p class="footer-copy">&copy; 2026 YuhTv Inc. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- script.js dihapus dari sini agar tidak error -->
</body>
</html>