@extends('layouts.dashboard')

@section('title', 'Registration')

@section('content')
    <livewire:dashboard.competition-registration-detail :competition="$competition" />
@endsection
