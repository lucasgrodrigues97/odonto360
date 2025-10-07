@extends('layouts.app')

@section('title', 'Dashboard - Odonto360')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            Dashboard
        </h1>
    </div>
</div>

@if(auth()->user()->isPatient())
    @include('dashboard.patient')
@elseif(auth()->user()->isDentist())
    @include('dashboard.dentist')
@elseif(auth()->user()->isAdmin())
    @include('dashboard.admin')
@endif
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Load dashboard data
    loadDashboardData();
    
    // Set up auto-refresh every 5 minutes
    setInterval(loadDashboardData, 300000);
});

function loadDashboardData() {
    // This will be implemented based on user role
    console.log('Loading dashboard data...');
}
</script>
@endsection
