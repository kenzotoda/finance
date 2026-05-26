<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Finance') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .auth-shell {
                min-height: 100vh;
                display: grid;
                grid-template-columns: 1fr;
                background:
                    radial-gradient(70rem 40rem at 10% -10%, #dbeafe 0, rgba(219, 234, 254, 0) 50%),
                    radial-gradient(65rem 40rem at 100% 110%, #bfdbfe 0, rgba(191, 219, 254, 0) 50%),
                    linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
            }

            .auth-left {
                display: none;
            }

            .auth-right {
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 2.5rem 1.25rem;
            }

            .auth-right::before,
            .auth-right::after {
                content: "";
                position: absolute;
                border-radius: 9999px;
                pointer-events: none;
                filter: blur(46px);
            }

            .auth-right::before {
                width: 17rem;
                height: 17rem;
                background: rgba(96, 165, 250, 0.4);
                top: 1.5rem;
                left: -4rem;
            }

            .auth-right::after {
                width: 20rem;
                height: 20rem;
                background: rgba(59, 130, 246, 0.25);
                right: -6rem;
                bottom: -4rem;
            }

            .auth-container {
                position: relative;
                width: 100%;
                max-width: 420px;
                z-index: 1;
            }

            .auth-card {
                border-radius: 20px;
                border: 1px solid rgba(147, 197, 253, 0.6);
                background: rgba(255, 255, 255, 0.84);
                backdrop-filter: blur(10px);
                box-shadow:
                    0 22px 44px rgba(30, 64, 175, 0.16),
                    inset 0 1px 0 rgba(255, 255, 255, 0.6);
                padding: 1.35rem;
            }

            .back-home {
                margin-top: 1.1rem;
                text-align: center;
                font-size: 0.9rem;
            }

            .back-home a {
                color: #2563eb;
                font-weight: 600;
                text-decoration: none;
            }

            .back-home a:hover {
                color: #1d4ed8;
            }

            @media (min-width: 640px) {
                .auth-card {
                    padding: 1.6rem;
                }
            }

            @media (min-width: 1024px) {
                .auth-shell {
                    grid-template-columns: 1.1fr 1fr;
                }

                .auth-left {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    padding: 2.8rem;
                    color: #fff;
                    background:
                        radial-gradient(26rem 26rem at 90% -10%, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0) 60%),
                        radial-gradient(20rem 20rem at 10% 100%, rgba(30, 64, 175, 0.45), rgba(30, 64, 175, 0) 70%),
                        linear-gradient(140deg, #1d4ed8 0%, #2563eb 45%, #0ea5e9 100%);
                }

                .auth-title {
                    max-width: 360px;
                    font-size: 2rem;
                    line-height: 1.2;
                    font-weight: 700;
                    margin-top: 1.25rem;
                    margin-bottom: 0.75rem;
                }

                .auth-subtitle {
                    max-width: 400px;
                    color: rgba(219, 234, 254, 0.95);
                    font-size: 1rem;
                    line-height: 1.6;
                }

                .auth-copy {
                    color: rgba(219, 234, 254, 0.8);
                    font-size: 0.9rem;
                }
            }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="auth-shell">
            <div class="auth-left">
                <a href="{{ url('/') }}">
                    <x-finance-brand size="lg" variant="light" />
                </a>

                <div>
                    <h2 class="auth-title">
                        Seu financeiro organizado, sem complicação.
                    </h2>
                    <p class="auth-subtitle">
                        Acompanhe despesas fixas, lucros, impostos e faturas de cartão em um painel unificado.
                    </p>
                </div>

                <p class="auth-copy">&copy; {{ date('Y') }} Finance</p>
            </div>

            <div class="auth-right">
                <div class="auth-container">
                    <div class="mb-8 lg:hidden">
                        <a href="{{ url('/') }}" class="inline-flex">
                            <x-finance-brand size="md" variant="dark" />
                        </a>
                    </div>

                    <div class="auth-card">
                        {{ $slot }}
                    </div>

                    <p class="back-home">
                        <a href="{{ url('/') }}">
                            Voltar para início
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
