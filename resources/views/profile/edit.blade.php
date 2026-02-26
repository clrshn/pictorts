@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div style="background:#333; color:#fff; padding:10px 20px; font-weight:600; font-size:13px;">
                    <i class="fas fa-user"></i> Profile Settings
                </div>
                <div style="padding:20px;">
                    @if(session('status') === 'profile-updated')
                        <div class="alert alert-success" style="margin-bottom:20px;">
                            Profile updated successfully!
                        </div>
                    @endif

                    <!-- Profile Information -->
                    <div class="table-card" style="margin-bottom:20px;">
                        <div style="background:#f8f9fa; padding:15px; font-weight:600; font-size:14px; border-bottom:1px solid #dee2e6;">
                            <i class="fas fa-info-circle"></i> Profile Information
                        </div>
                        <div style="padding:20px;">
                            <form method="post" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                                            @error('name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email (Username) <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                                            @error('email')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Password Update -->
                    <div class="table-card" style="margin-bottom:20px;">
                        <div style="background:#f8f9fa; padding:15px; font-weight:600; font-size:14px; border-bottom:1px solid #dee2e6;">
                            <i class="fas fa-key"></i> Update Password
                        </div>
                        <div style="padding:20px;">
                            <form method="post" action="{{ route('password.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Current Password <span class="text-danger">*</span></label>
                                            <input type="password" name="current_password" class="form-control" required>
                                            @error('current_password')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>New Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control" required>
                                            @error('password')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Confirm New Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control" required>
                                            @error('password_confirmation')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="table-card">
                        <div style="background:#f8f9fa; padding:15px; font-weight:600; font-size:14px; border-bottom:1px solid #dee2e6;">
                            <i class="fas fa-info-circle"></i> Account Information
                        </div>
                        <div style="padding:20px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div style="margin-bottom:15px;">
                                        <strong>Role:</strong> 
                                        <span class="badge {{ auth()->user()->isAdmin() ? 'badge-danger' : 'badge-primary' }}" style="margin-left:8px;">
                                            {{ strtoupper(auth()->user()->role) }}
                                        </span>
                                    </div>
                                    <div style="margin-bottom:15px;">
                                        <strong>Office:</strong> {{ auth()->user()->office?->code ?? 'Not Assigned' }}
                                    </div>
                                    <div style="margin-bottom:15px;">
                                        <strong>Member Since:</strong> {{ auth()->user()->created_at->format('F d, Y') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="margin-bottom:15px;">
                                        <strong>Email Verified:</strong> 
                                        {{ auth()->user()->hasVerifiedEmail() ? '✅ Yes' : '❌ No' }}
                                    </div>
                                    <div style="margin-bottom:15px;">
                                        <strong>Last Updated:</strong> {{ auth()->user()->updated_at->format('F d, Y H:i A') }}
                                    </div>
                                    @if(auth()->user()->isAdmin())
                                        <div style="margin-bottom:15px;">
                                            <strong>Admin Access:</strong> ✅ Full System Access
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
