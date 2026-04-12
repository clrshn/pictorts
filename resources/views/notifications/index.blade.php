@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Email Notification Testing</h3>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>Note:</strong> Make sure you have configured your email settings in the .env file before testing.
                            </div>
                        </div>
                    </div>

                    <!-- Test Notification -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Send Test Notification</h5>
                            <button class="btn btn-primary" onclick="sendTestNotification()">
                                <i class="fas fa-envelope"></i> Send Test Email
                            </button>
                        </div>
                    </div>

                    <hr>

                    <!-- Document Notification -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Document Notification</h5>
                            <form id="documentForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doc_user">Select User:</label>
                                            <select name="user_id" id="doc_user" class="form-control" required>
                                                @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doc_title">Document Title:</label>
                                            <input type="text" name="document_title" id="doc_title" class="form-control" value="Test Document" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doc_status">Status:</label>
                                            <select name="status" id="doc_status" class="form-control" required>
                                                <option value="APPROVED">Approved</option>
                                                <option value="REJECTED">Rejected</option>
                                                <option value="PENDING">Pending</option>
                                                <option value="PROCESSING">Processing</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-file-alt"></i> Send Document Notification
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <!-- Financial Notification -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Financial Notification</h5>
                            <form id="financialForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fin_user">Select User:</label>
                                            <select name="user_id" id="fin_user" class="form-control" required>
                                                @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fin_type">Record Type:</label>
                                            <input type="text" name="record_type" id="fin_type" class="form-control" value="Invoice" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fin_amount">Amount:</label>
                                            <input type="text" name="amount" id="fin_amount" class="form-control" value="$1,000.00" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fin_status">Status:</label>
                                            <select name="status" id="fin_status" class="form-control" required>
                                                <option value="PAID">Paid</option>
                                                <option value="PENDING">Pending</option>
                                                <option value="OVERDUE">Overdue</option>
                                                <option value="CANCELLED">Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-dollar-sign"></i> Send Financial Notification
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <!-- Task Notification -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Task Assignment Notification</h5>
                            <form id="taskForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="task_user">Assign to User:</label>
                                            <select name="user_id" id="task_user" class="form-control" required>
                                                @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="task_title">Task Title:</label>
                                            <input type="text" name="task_title" id="task_title" class="form-control" value="Complete Document Review" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="task_desc">Description:</label>
                                            <textarea name="description" id="task_desc" class="form-control" rows="2" required>Please review and approve the pending documents</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="task_due">Due Date:</label>
                                            <input type="date" name="due_date" id="task_due" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-tasks"></i> Send Task Notification
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <!-- Welcome Email -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Welcome Email</h5>
                            <form id="welcomeForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="welcome_user">Select User:</label>
                                            <select name="user_id" id="welcome_user" class="form-control" required>
                                                @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-secondary">
                                                <i class="fas fa-user-plus"></i> Send Welcome Email
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendTestNotification() {
    fetch('/notifications/test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        alert('Test notification sent successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending test notification');
    });
}

// Document form submission
document.getElementById('documentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/notifications/document', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert('Document notification sent successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending document notification');
    });
});

// Financial form submission
document.getElementById('financialForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/notifications/financial', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert('Financial notification sent successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending financial notification');
    });
});

// Task form submission
document.getElementById('taskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/notifications/task', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert('Task notification sent successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending task notification');
    });
});

// Welcome form submission
document.getElementById('welcomeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/notifications/welcome', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert('Welcome email sent successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending welcome email');
    });
});

// Set today's date as default for task due date
document.getElementById('task_due').valueAsDate = new Date();
</script>
@endsection
