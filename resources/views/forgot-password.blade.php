@extends('layouts.auth')
@section('title', 'Forgot password')
@section('content')
<x-auth-card title="Reset your password" description="Enter your account email and we will send a secure reset link.">
    @if (session('status'))<p class="mt-5 border border-status-success/30 bg-status-success/10 p-3 text-sm text-status-success-ink">{{ session('status') }}</p>@endif
    <form class="mt-7 space-y-5" method="POST" action="{{ route('password.email') }}">@csrf
        <label class="block text-sm font-medium">Email<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>@error('email')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <button class="w-full bg-catalyst-primary px-4 py-3 font-medium text-white" type="submit">Send reset link</button>
    </form>
    <a class="mt-6 block text-center text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('login') }}">Back to sign in</a>
</x-auth-card>
@endsection
