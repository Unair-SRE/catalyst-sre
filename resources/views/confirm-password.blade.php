@extends('layouts.auth')
@section('title', 'Confirm password')
@section('content')
<x-auth-card title="Confirm your password" description="For your security, confirm your password before continuing.">
    <form class="mt-7 space-y-5" method="POST" action="{{ route('password.confirm.store') }}">@csrf
        <label class="block text-sm font-medium">Password<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="password" name="password" autocomplete="current-password" required autofocus>@error('password')<span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span>@enderror</label>
        <button class="w-full bg-catalyst-primary px-4 py-3 font-medium text-white" type="submit">Confirm password</button>
    </form>
</x-auth-card>
@endsection
