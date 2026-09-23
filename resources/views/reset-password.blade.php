@extends('layouts.auth')
@section('title', 'Reset password')
@section('content')
<x-auth-card title="Choose a new password" description="Use a strong password that you do not reuse elsewhere.">
    <form class="mt-7 space-y-5" method="POST" action="{{ route('password.update') }}">@csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <label class="block text-sm font-medium">Email<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" name="email" value="{{ old('email', $request->email) }}" required>@error('email')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-medium">New password<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="password" name="password" autocomplete="new-password" required>@error('password')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-medium">Confirm password<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button class="w-full bg-catalyst-primary px-4 py-3 font-medium text-white" type="submit">Reset password</button>
    </form>
</x-auth-card>
@endsection
