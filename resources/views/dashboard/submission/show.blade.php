@extends('layouts.dashboard')

@section('title', 'Submission')

@section('content')
    <livewire:dashboard.submission-detail :competition="$competition" :stage="$stage" />
@endsection
