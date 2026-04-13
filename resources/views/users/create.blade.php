<x-app-layout>
    <x-slot name="header">
        <h1>Create User</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('users.index') }}">User Management</a> / Create User</div>
    </x-slot>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.showNotification === 'function') {
                    window.showNotification({
                        type: 'success',
                        title: 'Success!',
                        message: '{{ session('success') }}',
                        duration: 3000
                    });
                }
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.showNotification === 'function') {
                    window.showNotification({
                        type: 'danger',
                        title: 'Error!',
                        message: '{{ session('error') }}',
                        duration: 3000
                    });
                }
            });
        </script>
    @endif

    <div class="table-card">
                <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
                    <i class="fas fa-user-plus"></i> 
                </div>
                <div style="padding:20px;">
                    @include('components.notifications')

                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="EMAIL (USERNAME)" required>
                                    <small class="text-muted">Format: EMAIL (USERNAME)</small>
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Office <span class="text-danger">*</span></label>
                                    <select name="office_id" class="form-control" required>
                                        <option value="">Select Office</option>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                                                {{ $office->code }} – {{ $office->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('office_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Role <span class="text-danger">*</span></label>
                                    <select name="role" class="form-control" required>
                                        <option value="">Select Role</option>
                                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    @error('role')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required>
                                    @error('password')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                    @error('password_confirmation')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Important:</strong> After creating the user, provide them with:
                            <ul class="mb-0 mt-2">
                                <li><strong>Username:</strong> {{ old('email') ?: '[email will be shown here]' }}</li>
                                <li><strong>Password:</strong> [password will be shown here]</li>
                                <li><strong>Login URL:</strong> {{ url('/login') }}</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn-red">
                                <i class="fas fa-save"></i> Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn-gray">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameField = document.querySelector('input[name="name"]');
    const emailField = document.querySelector('input[name="email"]');
    
    if (nameField && emailField) {
        nameField.addEventListener('input', function() {
            const name = this.value.trim();
            if (name) {
                // Extract username from name (first word or convert to lowercase without spaces)
                const username = name.split(' ')[0].toLowerCase();
                const currentEmail = emailField.value.trim();
                
                // If email field is empty or doesn't have parentheses, update it
                if (!currentEmail || !currentEmail.includes('(')) {
                    emailField.value = `${username} (${username.toUpperCase()})`;
                } else {
                    // Update only the username part in parentheses
                    const emailPart = currentEmail.split('(')[0].trim();
                    emailField.value = `${emailPart} (${username.toUpperCase()})`;
                }
            }
        });
    }
});
</script>
</x-app-layout>
