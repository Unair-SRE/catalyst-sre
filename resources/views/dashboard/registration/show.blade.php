@extends('layouts.dashboard')

@section('title', 'Registration')

@section('content')
    <livewire:dashboard.registration-detail :competition="$competition" />
@endsection
