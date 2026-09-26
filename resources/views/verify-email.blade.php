@extends('layouts.auth')
@section('title', 'Verify email')
@section('content')
    <x-auth-card title="Check your email" description="Enter the six-digit code sent to {{ auth()->user()->email }}. The code expires in five minutes.">
        @if (session('status') === 'verification-code-sent')
            <p class="mt-5 border border-status-success/30 bg-status-success/10 p-3 text-sm leading-5 text-status-success-ink" role="status">A new verification code has been sent.</p>
        @endif

        <form class="mt-7 space-y-6" method="POST" action="{{ route('verification.verify-otp') }}">
            @csrf
            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="otp">Verification code</label>
                <input class="mt-2 h-14 w-full border border-neutral-200 bg-white px-3 text-center font-mono text-2xl tracking-[0.45em] text-catalyst-ink outline-none transition-colors placeholder:text-slate-400 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="otp" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus @error('otp') aria-invalid="true" aria-describedby="otp-error" @enderror>
                @error('otp')<p class="mt-2 text-sm text-status-error-ink" id="otp-error">{{ $message }}</p>@enderror
            </div>
            <button class="h-12 w-full bg-catalyst-primary px-4 text-sm font-medium text-white transition-colors hover:bg-catalyst-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" type="submit">Verify email</button>
        </form>

        <form class="mt-4" method="POST" action="{{ route('verification.send') }}" data-verification-resend>
            @csrf
            <button class="h-12 w-full border border-catalyst-primary px-4 text-sm font-medium text-catalyst-primary transition-colors hover:bg-catalyst-primary/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:border-neutral-300 disabled:text-catalyst-muted disabled:opacity-60" type="submit" disabled>
                <span data-verification-countdown>Resend available in 60s</span>
                <span class="hidden" data-verification-ready>Resend code</span>
            </button>
        </form>

        <form class="mt-5 text-center" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="font-sans text-sm text-catalyst-muted underline-offset-4 transition-colors hover:text-catalyst-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" type="submit">Sign out</button>
        </form>
    </x-auth-card>
@endsection
