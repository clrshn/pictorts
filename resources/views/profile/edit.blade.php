
@section('content')
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

    @if(session('status') === 'photo-updated')
        <div style="
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: 500;
        ">
            Profile photo updated successfully!
        </div>
    @endif

    @if(session('status') === 'photo-removed')
        <div style="
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: 500;
        ">
            Profile photo removed successfully!
        </div>
    @endif

                    <!-- Profile Photo -->
    <div class="table-card" style="margin-bottom:20px;">
        <div class="table-header" style="display:flex; justify-content:flex-start; align-items:center;">
            <i class="fas fa-camera" style="margin-right:8px; color:#8b0000;"></i>
            <h3 style="margin:0; color:#333;">Profile Photo</h3>
        </div>
        <div style="padding:20px;">
            <form method="post" action="{{ route('profile.photo.upload') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:flex; align-items:center; gap:20px; margin-bottom:20px;">
                    <div style="text-align:center;">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Current Profile" style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid #c0392b;">
                        @else
                            <div style="width:100px;height:100px;background:linear-gradient(135deg,#c0392b,#8b0000);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:36px;">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <div style="margin-top:8px; font-size:12px; color:#666;">Current Photo</div>
                    </div>
                    <div style="flex:1;">
                        <div class="form-group">
                            <label>Upload New Photo</label>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:15px;">
                                <button type="button" onclick="document.getElementById('fileInput').click()" 
                                        class="upload-option-btn" 
                                        onmouseover="this.style.transform='translateY(-2px)'" 
                                        onmouseout="this.style.transform='translateY(0)'"
                                        style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                                    <i class="fas fa-folder-open" style="font-size:20px; margin-bottom:8px;"></i>
                                    <div style="font-weight:600;">Choose File</div>
                                    <div style="font-size:11px; opacity:0.9;">Browse from device</div>
                                </button>
                                <button type="button" onclick="document.getElementById('cameraInput').click()" 
                                        class="upload-option-btn"
                                        onmouseover="this.style.transform='translateY(-2px)'" 
                                        onmouseout="this.style.transform='translateY(0)'"
                                        style="background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);">
                                    <i class="fas fa-camera" style="font-size:20px; margin-bottom:8px;"></i>
                                    <div style="font-weight:600;">Take Photo</div>
                                    <div style="font-size:11px; opacity:0.9;">Use camera</div>
                                </button>
                            </div>
                            <input type="file" id="fileInput" name="profile_photo" accept="image/*" class="form-control" onchange="previewImage(this)" style="display:none;">
                            <input type="file" id="cameraInput" name="profile_photo" accept="image/*" capture="camera" class="form-control" onchange="previewImage(this)" style="display:none;">
                            <div style="margin-top:12px; padding:12px; background:linear-gradient(135deg, rgba(37,99,235,0.05) 0%, rgba(234,88,12,0.05) 100%); border-radius:8px; border-left:4px solid #2980b9;">
                                <div style="display:flex; align-items:center; gap:8px; font-size:12px; color:#475569;">
                                    <i class="fas fa-info-circle" style="color:#2980b9;"></i>
                                    <span><strong>Tip:</strong> You can either choose a file from your device or take a new photo using your camera. Max file size: 2MB</span>
                                </div>
                            </div>
                            @error('profile_photo')
                                <div style="color:#dc3545; font-size:12px; margin-top:8px; padding:8px; background:#fef2f2; border-radius:6px; border-left:3px solid #dc2626;">
                                    <i class="fas fa-exclamation-triangle" style="margin-right:4px;"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div id="previewContainer" style="display:none; margin-top:10px;">
                            <div style="font-size:12px; color:#666; margin-bottom:5px;">Preview:</div>
                            <img id="previewImage" src="" alt="Preview" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid #ddd;">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-red">
                        <i class="fas fa-upload"></i> Upload Photo
                    </button>
                    @if(auth()->user()->profile_photo_path)
                        <a href="{{ route('profile.photo.remove') }}" method="post" onclick="return confirm('Are you sure you want to remove your profile photo?')" style="margin-left:10px; text-decoration:none;">
                            <button type="button" class="btn-gray">
                                <i class="fas fa-trash"></i> Remove Photo
                            </button>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="table-card" style="margin-bottom:20px;">
        <div class="table-header" style="display:flex; justify-content:flex-start; align-items:center;">
            <i class="fas fa-user" style="margin-right:8px; color:#8b0000;"></i>
            <h3 style="margin:0; color:#333;">Profile Information</h3>
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
                        <label>Email (Username) <span style="color:#dc3545;">*</span></label>
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
<script>
function previewImage(input) {
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const fileInput = document.getElementById('fileInput');
    const cameraInput = document.getElementById('cameraInput');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Check file size (2MB limit)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            input.value = '';
            previewContainer.style.display = 'none';
            return;
        }
        
        // Check file type
        if (!file.type.match('image.*')) {
            alert('Please select an image file');
            input.value = '';
            previewContainer.style.display = 'none';
            return;
        }
        
        // Clear the other input to prevent conflicts
        if (input.id === 'fileInput') {
            cameraInput.value = '';
        } else {
            fileInput.value = '';
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}

// Handle form submission to ensure the correct file is sent
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route("profile.photo.upload") }}"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('fileInput');
            const cameraInput = document.getElementById('cameraInput');
            
            // If camera input has a file, copy it to the main file input
            if (cameraInput.files && cameraInput.files[0]) {
                fileInput.files = cameraInput.files;
            }
        });
    }
});
</script>
</x-app-layout>
