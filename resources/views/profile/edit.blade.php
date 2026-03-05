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
                    
                    @if(session('status') === 'password-updated')
                        <div class="alert alert-info" style="margin-bottom:20px;">
                            Password updated successfully!
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
                                    <button type="submit" class="btn-red">
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
                                            <div class="input-group">
                                                <input type="password" name="password" id="newPassword" class="form-control" required>
                                                <button type="button" class="btn-gray" onclick="togglePassword('newPassword', this)" style="border-left: none;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div id="passwordStrength" style="margin-top: 5px; font-size: 12px;"></div>
                                            <div id="passwordRequirements" style="margin-top: 5px; font-size: 11px; color: #666;">
                                                <div id="req-length">• At least 8 characters</div>
                                                <div id="req-uppercase">• At least 1 uppercase letter</div>
                                                <div id="req-lowercase">• At least 1 lowercase letter</div>
                                                <div id="req-number">• At least 1 number</div>
                                                <div id="req-special">• At least 1 special character</div>
                                            </div>
                                            @error('password')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Confirm New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" required>
                                                <button type="button" class="btn-gray" onclick="togglePassword('confirmPassword', this)" style="border-left: none;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div id="passwordMatch" style="margin-top: 5px; font-size: 12px;"></div>
                                            @error('password_confirmation')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn-orange" id="updatePasswordBtn">
                                        <i class="fas fa-key"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        function togglePassword(inputId, button) {
                            const input = document.getElementById(inputId);
                            const icon = button.querySelector('i');
                            
                            if (input.type === 'password') {
                                input.type = 'text';
                                icon.classList.remove('fa-eye');
                                icon.classList.add('fa-eye-slash');
                            } else {
                                input.type = 'password';
                                icon.classList.remove('fa-eye-slash');
                                icon.classList.add('fa-eye');
                            }
                        }

                        function checkPasswordStrength(password) {
                            const requirements = {
                                length: password.length >= 8,
                                uppercase: /[A-Z]/.test(password),
                                lowercase: /[a-z]/.test(password),
                                number: /[0-9]/.test(password),
                                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                            };

                            // Update requirement indicators
                            document.getElementById('req-length').style.color = requirements.length ? '#28a745' : '#666';
                            document.getElementById('req-uppercase').style.color = requirements.uppercase ? '#28a745' : '#666';
                            document.getElementById('req-lowercase').style.color = requirements.lowercase ? '#28a745' : '#666';
                            document.getElementById('req-number').style.color = requirements.number ? '#28a745' : '#666';
                            document.getElementById('req-special').style.color = requirements.special ? '#28a745' : '#666';

                            // Calculate strength
                            const passedRequirements = Object.values(requirements).filter(Boolean).length;
                            let strength = '';
                            let strengthColor = '';
                            
                            if (passedRequirements === 0) {
                                strength = '';
                            } else if (passedRequirements <= 2) {
                                strength = 'Weak';
                                strengthColor = '#dc3545';
                            } else if (passedRequirements <= 4) {
                                strength = 'Medium';
                                strengthColor = '#ffc107';
                            } else {
                                strength = 'Strong';
                                strengthColor = '#28a745';
                            }

                            const strengthDiv = document.getElementById('passwordStrength');
                            if (strength) {
                                strengthDiv.innerHTML = `<span style="color: ${strengthColor}">Password Strength: ${strength}</span>`;
                            } else {
                                strengthDiv.innerHTML = '';
                            }

                            return passedRequirements === 5; // Return true if all requirements met
                        }

                        function checkPasswordMatch() {
                            const newPassword = document.getElementById('newPassword').value;
                            const confirmPassword = document.getElementById('confirmPassword').value;
                            const matchDiv = document.getElementById('passwordMatch');

                            if (confirmPassword === '') {
                                matchDiv.innerHTML = '';
                                return false;
                            }

                            if (newPassword === confirmPassword) {
                                matchDiv.innerHTML = '<span style="color: #28a745">✓ Passwords match</span>';
                                return true;
                            } else {
                                matchDiv.innerHTML = '<span style="color: #dc3545">✗ Passwords do not match</span>';
                                return false;
                            }
                        }

                        function validatePasswordForm() {
                            const newPassword = document.getElementById('newPassword').value;
                            const confirmPassword = document.getElementById('confirmPassword').value;
                            const currentPassword = document.querySelector('input[name="current_password"]').value;
                            const submitBtn = document.getElementById('updatePasswordBtn');

                            const isStrong = checkPasswordStrength(newPassword);
                            const isMatch = checkPasswordMatch();
                            const hasCurrentPassword = currentPassword.length > 0;

                            // Enable/disable submit button
                            submitBtn.disabled = !(isStrong && isMatch && hasCurrentPassword);
                            
                            if (!submitBtn.disabled) {
                                submitBtn.style.opacity = '1';
                                submitBtn.style.cursor = 'pointer';
                            } else {
                                submitBtn.style.opacity = '0.6';
                                submitBtn.style.cursor = 'not-allowed';
                            }
                        }

                        // Event listeners
                        document.addEventListener('DOMContentLoaded', function() {
                            const newPasswordInput = document.getElementById('newPassword');
                            const confirmPasswordInput = document.getElementById('confirmPassword');
                            const currentPasswordInput = document.querySelector('input[name="current_password"]');

                            newPasswordInput.addEventListener('input', validatePasswordForm);
                            confirmPasswordInput.addEventListener('input', validatePasswordForm);
                            currentPasswordInput.addEventListener('input', validatePasswordForm);

                            // Initial validation
                            validatePasswordForm();
                        });
                    </script>

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
