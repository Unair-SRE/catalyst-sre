@extends('layouts.dashboard')

@section('title', 'Competition Payment')

@section('content')
    <livewire:dashboard.competition-payment-form :competition="$competition" />
@endsection
