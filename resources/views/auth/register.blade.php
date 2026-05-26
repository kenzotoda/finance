<x-guest-layout>
    <div style="margin-bottom: 1.2rem;">
        <div style="margin-bottom: 0.6rem; display: inline-flex; border-radius: 14px; padding: 0.35rem 0.5rem; background: linear-gradient(135deg, rgba(59,130,246,0.12), rgba(14,165,233,0.08)); border: 1px solid rgba(59,130,246,0.18);">
            <x-finance-brand size="sm" variant="dark" />
        </div>
        <h1 style="margin: 0; font-size: 1.4rem; font-weight: 700; color: #0f172a;">Criar conta</h1>
        <p style="margin-top: 0.3rem; margin-bottom: 0; font-size: 0.92rem; color: #475569;">Preencha os dados abaixo para começar.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar senha" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Criar conta
        </button>

        <p class="text-center text-sm text-slate-600">
            Já tem conta?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 transition hover:text-blue-700">
                Entrar
            </a>
        </p>
    </form>
</x-guest-layout>
