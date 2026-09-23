@extends('layouts.public')

@section('title', 'Home')

@section('content')
    <x-ui.container class="py-16 sm:py-24">
        <h1 class="font-display text-3xl font-medium tracking-tight sm:text-4xl">Catalyst 2026</h1>
        <p class="mt-4 max-w-xl text-catalyst-ink/70">Competition, collaboration, and the Catalyst Summit in one participant experience.</p>
        <div class="mt-7 flex flex-wrap gap-3"><a class="inline-flex bg-catalyst-primary px-5 py-3 font-medium text-white" href="{{ route('competitions.index') }}">Explore Competitions</a>@guest<a class="inline-flex border border-catalyst-primary px-5 py-3 font-medium text-catalyst-primary" href="{{ route('login') }}">Sign in</a>@endguest</div>
    </x-ui.container>
@endsection
