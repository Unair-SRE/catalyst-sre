@extends('layouts.auth')
@section('title', 'Reset password')
@section('content')
    <x-auth-card title="Choose a new password" description="Use a strong password that you do not reuse elsewhere.">
        <form class="mt-7 space-y-6" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="email">Email</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="email" type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="you@domain.com" autocomplete="email" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<p class="mt-2 text-sm text-status-error-ink" id="email-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="password">New password</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="password" type="password" name="password" placeholder="Your new password" autocomplete="new-password" required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')<p class="mt-2 text-sm text-status-error-ink" id="password-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="password_confirmation">Confirm password</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm your new password" autocomplete="new-password" required>
            </div>

            <button class="h-12 w-full bg-catalyst-primary px-4 text-sm font-medium text-white transition-colors hover:bg-catalyst-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" type="submit">Reset password</button>
        </form>
    </x-auth-card>
@endsection
