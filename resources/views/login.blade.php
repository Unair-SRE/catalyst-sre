@extends('layouts.auth')
@section('title', 'Sign in')
@section('content')
    <x-auth-card title="Welcome back">
        @if (session('status'))
            <p class="mt-5 border border-status-success/30 bg-status-success/10 p-3 text-sm leading-5 text-status-success-ink" role="status">{{ session('status') }}</p>
        @endif

        <form class="mt-7 space-y-6" method="POST" action="{{ route('login.store') }}">
            @csrf

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="email">Email</label>
                <input
                    class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@domain.com"
                    autocomplete="email"
                    required
                    autofocus
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                >
                @error('email')<p class="mt-2 text-sm text-status-error-ink" id="email-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="password">Password</label>
                <input
                    class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15"
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Your password"
                    autocomplete="current-password"
                    required
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                >
                @error('password')<p class="mt-2 text-sm text-status-error-ink" id="password-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3 font-sans text-sm">
                <label class="inline-flex min-h-6 cursor-pointer items-center gap-2 text-catalyst-ink">
                    <input class="size-4 border-neutral-300 accent-catalyst-primary" type="checkbox" name="remember">
                    Remember me
                </label>
                <a class="font-medium text-catalyst-primary underline-offset-4 transition-colors hover:text-catalyst-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" href="{{ route('password.request') }}">Forgot password?</a>
            </div>

            <button class="h-12 w-full bg-catalyst-primary px-4 text-sm font-medium text-white transition-colors hover:bg-catalyst-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" type="submit">Sign in</button>
        </form>

        <p class="mt-6 text-center font-sans text-sm text-catalyst-muted">New to Catalyst? <a class="font-medium text-catalyst-primary underline-offset-4 transition-colors hover:text-catalyst-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" href="{{ route('register') }}">Create an account</a></p>
    </x-auth-card>
@endsection
