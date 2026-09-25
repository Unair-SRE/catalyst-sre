@extends('layouts.auth')
@section('title', 'Forgot password')
@section('content')
    <x-auth-card title="Reset your password" description="Enter your account email and we will send a secure reset link.">
        @if (session('status'))
            <p class="mt-5 border border-status-success/30 bg-status-success/10 p-3 text-sm leading-5 text-status-success-ink" role="status">{{ session('status') }}</p>
        @endif

        <form class="mt-7 space-y-6" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div>
                <label class="block font-sans text-sm font-medium leading-5 text-zinc-900" for="email">Email</label>
                <input class="mt-2 h-12 w-full border border-neutral-200 bg-white px-3 text-sm text-catalyst-ink outline-none transition-colors placeholder:text-slate-500 hover:border-neutral-300 focus:border-status-info focus:ring-2 focus:ring-status-info/15" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@domain.com" autocomplete="email" required autofocus @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<p class="mt-2 text-sm text-status-error-ink" id="email-error">{{ $message }}</p>@enderror
            </div>
            <button class="h-12 w-full bg-catalyst-primary px-4 text-sm font-medium text-white transition-colors hover:bg-catalyst-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" type="submit">Send reset link</button>
        </form>

        <a class="mt-6 block text-center font-sans text-sm font-medium text-catalyst-primary underline-offset-4 transition-colors hover:text-catalyst-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-2" href="{{ route('login') }}">Back to sign in</a>
    </x-auth-card>
@endsection
