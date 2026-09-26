@extends('layouts.auth')
@section('title', 'Create account')
@section('content')
    <x-auth-card title="Create your account">
        <form class="mt-7 space-y-6" method="POST" action="{{ route('register.store') }}">
            @csrf

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="email">Email</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@domain.com" autocomplete="email" required autofocus @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<p class="mt-2 text-sm text-status-error-ink" id="email-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="name">Full name</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="name" name="name" value="{{ old('name') }}" placeholder="Your name" autocomplete="name" required @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')<p class="mt-2 text-sm text-status-error-ink" id="name-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="whatsapp">WhatsApp number</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Your contact" autocomplete="tel" required @error('whatsapp') aria-invalid="true" aria-describedby="whatsapp-error" @enderror>
                @error('whatsapp')<p class="mt-2 text-sm text-status-error-ink" id="whatsapp-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="password">Password</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="password" type="password" name="password" placeholder="Your password" autocomplete="new-password" required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')<p class="mt-2 text-sm text-status-error-ink" id="password-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="password_confirmation">Confirm password</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm your password" autocomplete="new-password" required>
            </div>

            <button class="h-12 w-full bg-catalyst-primary px-4 text-sm font-medium text-white transition-colors hover:bg-catalyst-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" type="submit">Create account</button>
        </form>

        <p class="mt-6 text-center font-sans text-sm text-catalyst-muted">Already registered? <a class="font-medium text-catalyst-primary underline-offset-4 transition-colors hover:text-catalyst-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" href="{{ route('login') }}">Sign in</a></p>
    </x-auth-card>
@endsection
