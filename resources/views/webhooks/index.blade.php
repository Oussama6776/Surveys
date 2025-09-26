@extends('layouts.app')

@section('title', 'Webhooks - ' . $survey->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Webhooks</h1>
                    <p class="text-muted mb-0">{{ $survey->title }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('webhooks.create', $survey) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Webhook
                    </a>
                    <a href="{{ route('surveys.show', $survey) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Survey
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($webhooks->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Webhook Configuration</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="webhooksTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Event Type</th>
                                    <th>URL</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Last Triggered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($webhooks as $webhook)
                                <tr>
                                    <td>
                                        <strong>{{ $webhook->name }}</strong>
                                        @if($webhook->description)
                                            <br><small class="text-muted">{{ $webhook->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $webhook->event_type }}</span>
                                    </td>
                                    <td>
                                        <code class="text-break">{{ $webhook->url }}</code>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $webhook->http_method }}</span>
                                    </td>
                                    <td>
                                        @if($webhook->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($webhook->last_triggered_at)
                                            <small>{{ $webhook->last_triggered_at->diffForHumans() }}</small>
                                        @else
                                            <small class="text-muted">Never</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('webhooks.show', [$survey, $webhook]) }}" 
                                               class="btn btn-primary btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('webhooks.edit', [$survey, $webhook]) }}" 
                                               class="btn btn-secondary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-info btn-sm" 
                                                    onclick="testWebhook({{ $webhook->id }})" 
                                                    title="Test">
                                                <i class="fas fa-play"></i>
                                            </button>
                                            <button class="btn btn-{{ $webhook->is_active ? 'warning' : 'success' }} btn-sm" 
                                                    onclick="toggleWebhook({{ $webhook->id }})" 
                                                    title="{{ $webhook->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $webhook->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="deleteWebhook({{ $webhook->id }})" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
    @else
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-link fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No webhooks configured</h4>
                <p class="text-muted">Create your first webhook to receive real-time notifications about survey events.</p>
                <a href="{{ route('webhooks.create', $survey) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Webhook
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Webhook Events Info -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Events</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Response Events</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <strong>response_submitted</strong> - When a response is submitted</li>
                                <li><i class="fas fa-check text-success"></i> <strong>survey_completed</strong> - When a survey is completed</li>
                                <li><i class="fas fa-check text-success"></i> <strong>response_deleted</strong> - When a response is deleted</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Survey Events</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <strong>survey_created</strong> - When a survey is created</li>
                                <li><i class="fas fa-check text-success"></i> <strong>survey_updated</strong> - When a survey is updated</li>
                                <li><i class="fas fa-check text-success"></i> <strong>survey_published</strong> - When a survey is published</li>
                                <li><i class="fas fa-check text-success"></i> <strong>survey_closed</strong> - When a survey is closed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function testWebhook(webhookId) {
    if (confirm('Send a test webhook?')) {
        fetch(`/surveys/{{ $survey->id }}/webhooks/${webhookId}/test`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Test webhook sent successfully!');
                location.reload();
            } else {
                alert('Test webhook failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error sending test webhook: ' + error.message);
        });
    }
}

function toggleWebhook(webhookId) {
    fetch(`/surveys/{{ $survey->id }}/webhooks/${webhookId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error toggling webhook: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error toggling webhook: ' + error.message);
    });
}

function deleteWebhook(webhookId) {
    if (confirm('Are you sure you want to delete this webhook?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/surveys/{{ $survey->id }}/webhooks/${webhookId}`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}

$(document).ready(function() {
    $('#webhooksTable').DataTable({
        "pageLength": 25,
        "order": [[ 5, "desc" ]]
    });
});
</script>
@endpush
@endsection
