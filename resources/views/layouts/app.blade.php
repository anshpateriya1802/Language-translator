<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TRANSLATOR</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- Global styles -->
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --info-color: #06b6d4;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --background-color: #f8fafc;
            --card-bg: #ffffff;
            --text-color: #1e293b;
            --border-radius: 0.5rem;
        }
        
        body {
            min-height: 100vh;
            background-color: var(--background-color);
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        }
        
        .success-flash {
            animation: successPulse 1.5s ease-in-out;
        }
        
        @keyframes successPulse {
            0%, 100% { 
                background-color: rgba(16, 185, 129, 0.1);
            }
            50% { 
                background-color: rgba(16, 185, 129, 0.3);
            }
        }
        
        .navbar {
            background-color: var(--card-bg);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .nav-item {
            transition: all 0.3s ease;
            margin: 0 0.25rem;
        }
        
        .nav-item:hover {
            transform: translateY(-2px);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--secondary-color) !important;
            border-radius: var(--border-radius);
            padding: 0.5rem 1rem !important;
        }
        
        .nav-link:hover, .nav-link:focus {
            color: var(--primary-color) !important;
            background-color: rgba(99, 102, 241, 0.1);
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(99, 102, 241, 0.1);
        }
        
        .dropdown-item:active {
            background-color: var(--primary-color);
        }
        
        .btn {
            font-weight: 600;
            border-radius: var(--border-radius);
            padding: 0.5rem 1.25rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        
        .card-header {
            border-bottom: none;
            padding: 1.25rem 1.5rem;
            background-color: var(--card-bg);
            font-weight: 600;
        }
        
        .alert {
            border-radius: var(--border-radius);
            border-left: 5px solid;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .alert-success {
            border-left-color: var(--success-color);
        }
        
        .alert-danger {
            border-left-color: var(--danger-color);
        }
        
        .alert-warning {
            border-left-color: var(--warning-color);
        }
        
        .alert-info {
            border-left-color: var(--info-color);
        }
        
        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .ripple {
            position: absolute;
            border-radius: 50%;
            transform: scale(0);
            background-color: rgba(255, 255, 255, 0.4);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    TRANSLATOR
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                    <i class="fas fa-home me-1"></i>{{ __('Home') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('translations.index') ? 'active' : '' }}" href="{{ route('translations.index') }}">
                                    <i class="fas fa-history me-1"></i>{{ __('My Translations') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('translate') ? 'active' : '' }}" href="{{ route('translate') }}">
                                    <i class="fas fa-language me-1"></i>{{ __('New Translation') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('translations.feedback') ? 'active' : '' }}" href="{{ route('translations.feedback') }}">
                                    <i class="fas fa-star me-1"></i>{{ __('Feedback Center') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('regional-phrases.*') ? 'active' : '' }}" href="{{ route('regional-phrases.index') }}">
                                    <i class="fas fa-globe me-1"></i>{{ __('Regional Phrases') }}
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-1"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success success-flash mb-4 animate__animated animate__fadeIn">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger mb-4 animate__animated animate__fadeIn">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Animation Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add hover effects to buttons
            $('.btn').hover(
                function() { $(this).addClass('animate__animated animate__pulse'); },
                function() { $(this).removeClass('animate__animated animate__pulse'); }
            );
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Add click ripple effect
            $('.btn').on('click', function(e) {
                var x = e.pageX - $(this).offset().left;
                var y = e.pageY - $(this).offset().top;
                
                var ripple = $('<span class="ripple"></span>');
                ripple.css({
                    left: x + 'px',
                    top: y + 'px'
                });
                
                $(this).append(ripple);
                
                setTimeout(function() {
                    ripple.remove();
                }, 700);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html> 