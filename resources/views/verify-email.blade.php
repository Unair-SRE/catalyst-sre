@extends('layouts.auth')
@section('title', 'Verify email')
@section('content')
<x-auth-card title="Check your email" description="Enter the six-digit code sent to {{ auth()->user()->email }}. The code expires in five minutes.">
    @if (session('status') === 'verification-code-sent')<p class="mt-5 border border-status-success/30 bg-status-success/10 p-3 text-sm text-status-success-ink">A new verification code has been sent.</p>@endif
    <form class="mt-7 space-y-5" method="POST" action="{{ route('verification.verify-otp') }}">@csrf
        <label class="block text-sm font-medium">Verification code<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3 text-center font-mono text-2xl tracking-[0.45em]" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>@error('otp')<span class="mt-2 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <button class="w-full bg-catalyst-primary px-4 py-3 font-medium text-white" type="submit">Verify email</button>
    </form>
    <form class="mt-4" method="POST" action="{{ route('verification.send') }}" data-verification-resend>@csrf<button class="w-full border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary disabled:cursor-not-allowed disabled:opacity-50" type="submit" disabled><span data-verification-countdown>Resend available in 60s</span><span class="hidden" data-verification-ready>Resend code</span></button></form>
    <form class="mt-4 text-center" method="POST" action="{{ route('logout') }}">@csrf<button class="text-sm text-catalyst-muted underline underline-offset-4" type="submit">Sign out</button></form>
</x-auth-card>
@endsection
