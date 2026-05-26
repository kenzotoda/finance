<x-guest-layout>
    <div style="margin-bottom: 1.2rem;">
        <div style="margin-bottom: 0.6rem; display: inline-flex; border-radius: 14px; padding: 0.35rem 0.5rem; background: linear-gradient(135deg, rgba(59,130,246,0.12), rgba(14,165,233,0.08)); border: 1px solid rgba(59,130,246,0.18);">
            <x-finance-brand size="sm" variant="dark" />
        </div>
        <h1 style="margin: 0; font-size: 1.4rem; font-weight: 700; color: #0f172a;">Entrar</h1>
        <p style="margin-top: 0.3rem; margin-bottom: 0; font-size: 0.92rem; color: #475569;">Acesse sua conta para continuar.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Lembrar de mim</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-blue-600 transition hover:text-blue-700" href="{{ route('password.request') }}">
                    Esqueceu a senha?
                </a>
            @endif
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Entrar
        </button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-600">
                Não tem conta?
                <a href="{{ route('register') }}" class="font-semibold text-blue-600 transition hover:text-blue-700">
                    Criar cadastro
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
