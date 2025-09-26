@extends('layouts.app')

@section('title', 'Files - ' . $survey->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">File Management</h1>
                    <p class="text-muted mb-0">{{ $survey->title }}</p>
                </div>
                <div class="btn-group">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                        <i class="fas fa-upload"></i> Upload Files
                    </button>
                    <button class="btn btn-success" onclick="exportFiles()">
                        <i class="fas fa-download"></i> Export List
                    </button>
                    <a href="{{ route('surveys.show', $survey) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Survey
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($files->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Uploaded Files</h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="selectAllFiles()">
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button class="btn btn-outline-danger" onclick="bulkDeleteFiles()">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="filesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </th>
                                    <th>File</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Question</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $file)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="file-checkbox" value="{{ $file->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $file->getFileTypeIcon() }} fa-2x text-primary mr-3"></i>
                                            <div>
                                                <strong>{{ $file->original_name }}</strong>
                                                <br><small class="text-muted">{{ $file->mime_type }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($file->file_type) }}</span>
                                    </td>
                                    <td>{{ $file->getFormattedSize() }}</td>
                                    <td>
                                        @if($file->question)
                                            <small>{{ Str::limit($file->question->question_text, 30) }}</small>
                                        @else
                                            <small class="text-muted">General</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $file->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ $file->getUrl() }}" 
                                               class="btn btn-primary btn-sm" 
                                               target="_blank" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('files.download', $file) }}" 
                                               class="btn btn-success btn-sm" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="deleteFile({{ $file->id }})" 
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
                <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No files uploaded</h4>
                <p class="text-muted">Upload files to share with your survey respondents or for your own use.</p>
                <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                    <i class="fas fa-upload"></i> Upload Files
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Files</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file_type">File Category</label>
                        <select class="form-control" id="file_type" name="file_type" required>
                            <option value="">Select category...</option>
                            <option value="image">Image</option>
                            <option value="document">Document</option>
                            <option value="video">Video</option>
                            <option value="audio">Audio</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="files">Select Files</label>
                        <input type="file" class="form-control-file" id="files" name="files[]" multiple required>
                        <small class="form-text text-muted">
                            Maximum file size: 10MB. Allowed types: Images, Documents, Videos, Audio
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_public" name="is_public">
                            <label class="form-check-label" for="is_public">
                                Make files publicly accessible
                            </label>
                        </div>
                    </div>
                    
                    <div id="upload-progress" class="progress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Files
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function selectAllFiles() {
    document.querySelectorAll('.file-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAll').checked = true;
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    document.querySelectorAll('.file-checkbox').forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function bulkDeleteFiles() {
    const selectedFiles = Array.from(document.querySelectorAll('.file-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedFiles.length === 0) {
        alert('Please select files to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedFiles.length} file(s)?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("files.bulk-delete", $survey) }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';
        
        selectedFiles.forEach(fileId => {
            const fileField = document.createElement('input');
            fileField.type = 'hidden';
            fileField.name = 'file_ids[]';
            fileField.value = fileId;
            form.appendChild(fileField);
        });
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteFile(fileId) {
    if (confirm('Are you sure you want to delete this file?')) {
        fetch(`/files/${fileId}`, {
            method: 'DELETE',
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
                alert('Error deleting file: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error deleting file: ' + error.message);
        });
    }
}

function exportFiles() {
    window.location.href = '{{ route("files.export", $survey) }}';
}

// File upload handling
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const progressBar = document.getElementById('upload-progress');
    const progressBarInner = progressBar.querySelector('.progress-bar');
    
    progressBar.style.display = 'block';
    
    fetch('{{ route("files.upload", $survey) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#uploadModal').modal('hide');
            location.reload();
        } else {
            alert('Upload failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Upload error: ' + error.message);
    })
    .finally(() => {
        progressBar.style.display = 'none';
        progressBarInner.style.width = '0%';
    });
});

$(document).ready(function() {
    $('#filesTable').DataTable({
        "pageLength": 25,
        "order": [[ 5, "desc" ]]
    });
});
</script>
@endpush
@endsection
