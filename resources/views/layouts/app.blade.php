<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Finance') }}</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .finance-app-shell {
                min-height: 100vh;
                background:
                    radial-gradient(70rem 42rem at 0% -10%, #dbeafe 0, rgba(219, 234, 254, 0) 55%),
                    radial-gradient(65rem 38rem at 100% 115%, #bfdbfe 0, rgba(191, 219, 254, 0) 52%),
                    linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
            }

            .finance-header-shell {
                border-bottom: 1px solid rgba(147, 197, 253, 0.5);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.86), rgba(255, 255, 255, 0.74));
                backdrop-filter: blur(6px);
            }

            .finance-main {
                position: relative;
                padding-top: 1.6rem;
                padding-bottom: 2rem;
            }

            .finance-main .bg-white {
                border: 1px solid #dbeafe;
                box-shadow: 0 16px 36px rgba(30, 64, 175, 0.08);
            }

            .finance-main .shadow-sm,
            .finance-main .shadow {
                box-shadow: 0 14px 30px rgba(30, 64, 175, 0.08) !important;
            }

            .finance-main table thead tr {
                background: #eff6ff !important;
            }

            .finance-main table thead th {
                color: #334155;
                font-weight: 700;
            }

            .finance-main table tbody tr {
                transition: background-color 0.18s ease;
            }

            .finance-main table tbody tr:hover {
                background: #f8fbff;
            }

            .finance-main .modal-content {
                border: 1px solid #bfdbfe;
                border-radius: 1rem;
                box-shadow: 0 24px 46px rgba(30, 64, 175, 0.2);
            }

            .finance-main .modal-header {
                border-bottom-color: #dbeafe;
                background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
            }

            .finance-main .modal-footer {
                border-top-color: #dbeafe;
            }

            .finance-main .btn-close {
                opacity: 0.7;
            }

            .finance-main .btn-close:hover {
                opacity: 1;
            }

            .finance-main input:not([type="checkbox"]):not([type="radio"]),
            .finance-main select,
            .finance-main textarea {
                border-color: #bfdbfe !important;
                border-radius: 0.7rem !important;
            }

            .finance-main input:focus,
            .finance-main select:focus,
            .finance-main textarea:focus {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.16) !important;
            }

            .finance-nav-links {
                margin-left: 3.5rem;
                gap: 0.5rem;
            }

            @media (min-width: 1024px) {
                .finance-nav-links {
                    margin-left: 5rem;
                }
            }

            .finance-stacked-sections {
                display: flex;
                flex-direction: column;
                gap: 2.5rem;
            }

            .finance-dollar-icon {
                display: inline-block;
                line-height: 1;
                width: auto;
                height: auto;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="finance-app-shell">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="finance-header-shell">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="finance-main">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @include('partials.flash')
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
