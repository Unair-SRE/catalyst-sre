@extends('layouts.auth')
@section('title', 'Confirm password')
@section('content')
    <x-auth-card title="Confirm your password" description="For your security, confirm your password before continuing.">
        <form class="mt-7 space-y-6" method="POST" action="{{ route('password.confirm.store') }}">
            @csrf
            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="password">Password</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="password" type="password" name="password" placeholder="Your password" autocomplete="current-password" required autofocus @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')<p class="mt-2 text-sm text-status-error-ink" id="password-error">{{ $message }}</p>@enderror
            </div>
            <button class="h-12 w-full bg-catalyst-primary px-4 text-sm font-medium text-white transition-colors hover:bg-catalyst-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" type="submit">Confirm password</button>
        </form>
    </x-auth-card>
@endsection
