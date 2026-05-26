<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Finance — Controle financeiro pessoal</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-white antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -left-32 top-0 h-96 w-96 rounded-full bg-blue-600/30 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-sky-500/25 blur-3xl"></div>
                <div class="absolute left-1/2 top-1/3 h-64 w-64 -translate-x-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            </div>

            <div class="relative flex min-h-screen flex-col items-center justify-center px-6 py-16">
                <div class="w-full max-w-lg text-center">
                    <div class="mb-8 flex justify-center">
                        <x-finance-brand size="xl" variant="light" />
                    </div>

                    <p class="mx-auto max-w-md text-lg leading-relaxed text-blue-100/90">
                        Organize despesas, receitas, faturas e impostos com clareza. Simples, moderno e feito para o seu dia a dia.
                    </p>

                    <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:justify-center">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-blue-700 shadow-lg shadow-blue-900/20 transition hover:bg-blue-50">
                            Entrar
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 px-8 py-3.5 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20">
                                Criar conta
                            </a>
                        @endif
                    </div>
                </div>

                <p class="absolute bottom-6 text-xs text-blue-200/60">
                    &copy; {{ date('Y') }} Finance
                </p>
            </div>
        </div>
    </body>
</html>
