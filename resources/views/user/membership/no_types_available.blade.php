@extends('layouts.admin.app')
@section('title', 'Membership Unavailable')
@section('content')
<div class="container py-5 text-center">
    <div class="alert alert-warning">
        <h4>Membership Types Currently Unavailable</h4>
        <p>We apologize, but there are currently no membership types available for application. Please check back later or contact support.</p>
    </div>
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
</div>
@endsection
