@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Analytics Dashboard</h1>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="refreshAnalytics()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Surveys</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $surveys->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Responses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $surveys->sum('responses_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Surveys</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $surveys->where('status', 'active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg. Completion Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">85%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Surveys Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Survey Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="surveysTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Survey Title</th>
                                    <th>Status</th>
                                    <th>Responses</th>
                                    <th>Completion Rate</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($surveys as $survey)
                                <tr>
                                    <td>
                                        <a href="{{ route('analytics.show', $survey) }}" class="text-decoration-none">
                                            {{ $survey->title }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $survey->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($survey->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $survey->responses_count }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: 85%">
                                                85%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $survey->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('analytics.show', $survey) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <a href="{{ route('analytics.export.pdf', $survey) }}" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('analytics.export.excel', $survey) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-file-excel"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function refreshAnalytics() {
    location.reload();
}

$(document).ready(function() {
    $('#surveysTable').DataTable({
        "pageLength": 25,
        "order": [[ 4, "desc" ]]
    });
});
</script>
@endpush
@endsection
