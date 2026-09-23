@extends('layouts.auth')
@section('title', 'Create account')
@section('content')
<x-auth-card title="Create your account" description="One account can lead one team and register for Catalyst competitions.">
    <form class="mt-7 space-y-5" method="POST" action="{{ route('register.store') }}">@csrf
        <label class="block text-sm font-medium">Full name<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>@error('name')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-medium">Email<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-medium">WhatsApp number<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" name="whatsapp" value="{{ old('whatsapp') }}" autocomplete="tel" required>@error('whatsapp')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-medium">Password<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="password" name="password" autocomplete="new-password" required>@error('password')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-medium">Confirm password<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button class="w-full bg-catalyst-primary px-4 py-3 font-medium text-white" type="submit">Create account</button>
    </form>
    <p class="mt-6 text-center text-sm text-catalyst-muted">Already registered? <a class="font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('login') }}">Sign in</a></p>
</x-auth-card>
@endsection
