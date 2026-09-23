@extends('layouts.auth')
@section('title', 'Sign in')
@section('content')
<x-auth-card title="Welcome back" description="Sign in to manage your Catalyst team and registrations.">
    @if (session('status'))<p class="mt-5 border border-status-success/30 bg-status-success/10 p-3 text-sm text-status-success-ink">{{ session('status') }}</p>@endif
    <form class="mt-7 space-y-5" method="POST" action="{{ route('login.store') }}">@csrf
        <label class="block text-sm font-medium">Email<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>@error('email')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-medium">Password<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="password" name="password" autocomplete="current-password" required>@error('password')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <div class="flex items-center justify-between gap-4"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Remember me</label><a class="text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('password.request') }}">Forgot password?</a></div>
        <button class="w-full bg-catalyst-primary px-4 py-3 font-medium text-white" type="submit">Sign in</button>
    </form>
    <p class="mt-6 text-center text-sm text-catalyst-muted">New to Catalyst? <a class="font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('register') }}">Create an account</a></p>
</x-auth-card>
@endsection
