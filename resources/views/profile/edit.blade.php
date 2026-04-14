
<x-app-layout>
    <x-slot name="header">
        <h1>Profile Settings</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a> / Profile Settings
        </div>
    </x-slot>

    @if(session('status') === 'profile-updated')
        <div style="
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: 500;
        ">
            Profile updated successfully!
        </div>
    @endif
    
    @if(session('status') === 'password-updated')
        <div style="
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: 500;
        ">
            Password updated successfully!
        </div>
    @endif

    
                    
    <!-- Profile Information -->
    <div class="table-card" style="margin-bottom:20px;">
        <div class="table-header" style="display:flex; justify-content:flex-start; align-items:center;">
            <i class="fas fa-user" style="margin-right:8px; color:#8b0000;"></i>
            <h3 style="margin:0; color:#333;">Personal Information</h3>
        </div>
        <div style="padding:20px;">
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px,1fr)); gap:20px;">
                    <div class="form-group">
                        <label>Name <span style="color:#dc3545;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div style="color:#dc3545; font-size:12px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Email<span style="color:#dc3545;">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div style="color:#dc3545; font-size:12px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group" style="margin-top:20px;">
                    <button type="submit" class="btn-red">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Update -->
    <div class="table-card" style="margin-bottom:20px;">
        <div class="table-header" style="display:flex; justify-content:flex-start; align-items:center;">
            <i class="fas fa-key" style="margin-right:8px; color:#8b0000;"></i>
            <h3 style="margin:0; color:#333;">Update Password</h3>
        </div>
        <div style="padding:20px;">
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px,1fr)); gap:20px;">
                    <div class="form-group">
                        <label>Current Password <span style="color:#dc3545;">*</span></label>
                        <input type="password" name="current_password" class="form-control" required>
                        @error('current_password')
                            <div style="color:#dc3545; font-size:12px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>New Password <span style="color:#dc3545;">*</span></label>
                        <div style="position:relative;">
                        <input type="password" name="password" id="newPassword"
                            class="form-control"
                            required
                            style="padding-right:45px;">

                        <button type="button"
                            onclick="togglePassword('newPassword', this)"
                            style="
                                position:absolute;
                                right:8px;
                                top:50%;
                                transform:translateY(-50%);
                                border:1px solid #ddd;
                                background:#fff;
                                padding:6px 8px;
                                border-radius:4px;
                                cursor:pointer;
                            ">
                            <i class="fas fa-eye" style="color:#555;"></i>
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
                            <div style="color:#dc3545; font-size:12px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password <span style="color:#dc3545;">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="password_confirmation" id="confirmPassword"
                                class="form-control"
                                required
                                style="padding-right:45px;">

                            <button type="button"
                                onclick="togglePassword('confirmPassword', this)"
                                style="
                                    position:absolute;
                                    right:8px;
                                    top:50%;
                                    transform:translateY(-50%);
                                    border:1px solid #ddd;
                                    background:#fff;
                                    padding:6px 8px;
                                    border-radius:4px;
                                    cursor:pointer;
                                ">
                                <i class="fas fa-eye" style="color:#555;"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" style="margin-top: 5px; font-size: 12px;"></div>
                        @error('password_confirmation')
                            <div style="color:#dc3545; font-size:12px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group" style="margin-top:20px;">
                    <button type="submit" class="btn-red" id="updatePasswordBtn" style="opacity:1;">
    <i class="fas fa-key"></i> Update Password
    </button>
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
                               submitBtn.style.opacity = '1';
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

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
