<aside class="sidebar">
    <!-- Navigation starts here -->

    <!-- Profile Section -->
    <div class="profile-section" style="text-align: center; padding: 20px;">
        <div class="profile-avatar" style="margin: 0 auto 20px auto; width: 80px; height: 80px;">
            @if(auth()->user() && auth()->user()->profile_photo_path)
                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            @else
                <div class="profile-avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
            @endif
        </div>
        <div class="profile-info">
            <div class="profile-name" style="text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">{{ auth()->user()->name ?? 'JOHN DOE' }}</div>
            <div class="profile-role" style="text-transform: uppercase; font-size: 12px; opacity: 0.8;">{{ auth()->user()->role ?? 'ADMINISTRATOR' }}</div>
        </div>
    </div>

    <!-- Navigation -->
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <a href="{{ route('todos.index') }}" class="nav-item {{ request()->routeIs('todos.*') ? 'active' : '' }}">
        <i class="fas fa-tasks"></i> To-Do
    </a>

    <!-- Documents Dropdown -->
    <div class="nav-dropdown">
        <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.index') && !request()->has('direction') ? 'active' : '' }}" onclick="toggleDocumentsDropdown()">
            <i class="fas fa-list"></i> All Documents <i class="fas fa-chevron-down" style="float: right; font-size: 10px; margin-top: 2px;"></i>
        </a>
        <div class="nav-sub" id="documentsDropdown">
            <a href="{{ route('documents.index', ['direction' => 'INCOMING']) }}" class="nav-item">
                <i class="fas fa-arrow-down"></i> Incoming Documents
            </a>
            <a href="{{ route('documents.index', ['direction' => 'OUTGOING']) }}" class="nav-item">
                <i class="fas fa-arrow-up"></i> Outgoing Documents
            </a>
        </div>
    </div>

    <!-- Financial Dropdown -->
    <div class="nav-dropdown">
        <a href="{{ route('financial.index') }}" class="nav-item {{ request()->routeIs('financial.index') && !request()->has('status') ? 'active' : '' }}" onclick="toggleFinancialDropdown()">
            <i class="fas fa-coins"></i> All Financial <i class="fas fa-chevron-down" style="float: right; font-size: 10px; margin-top: 2px;"></i>
        </a>
        <div class="nav-sub" id="financialDropdown">
            <a href="{{ route('financial.index', ['status' => 'ACTIVE']) }}" class="nav-item">
                <i class="fas fa-play-circle"></i> Active
            </a>
            <a href="{{ route('financial.index', ['status' => 'CANCELLED']) }}" class="nav-item">
                <i class="fas fa-times-circle"></i> Cancelled
            </a>
            <a href="{{ route('financial.index', ['status' => 'FINISHED']) }}" class="nav-item">
                <i class="fas fa-check-circle"></i> Finished
            </a>
        </div>
    </div>

    <!-- Spacer to push User Management to bottom -->
    <div style="flex: 1;"></div>

    @if(auth()->user() && auth()->user()->isAdmin())
    <!-- User Management (Admin Only) - Absolute Bottom -->
    <div class="nav-section" style="text-align: center; padding: 10px 0; margin: 0; position: absolute; bottom: 0; left: 0; right: 0;">
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" style="display: flex; justify-content: center; width: auto; padding: 12px 20px; white-space: nowrap;">
                <i class="fas fa-users"></i> User Management
            </a>
            <a href="{{ route('offices.index') }}" class="nav-item {{ request()->routeIs('offices.*') ? 'active' : '' }}" style="display: flex; justify-content: center; width: auto; padding: 12px 20px; white-space: nowrap;">
                <i class="fas fa-building"></i> Office Management
            </a>
        </div>
    </div>
    @endif
</aside>

<script>
let documentsDropdownOpen = false;
let financialDropdownOpen = false;

function toggleDocumentsDropdown() {
    const dropdown = document.getElementById('documentsDropdown');
    
    if (!documentsDropdownOpen) {
        // First click - go to all documents and open dropdown
        window.location.href = '{{ route("documents.index") }}';
        
        // Store state to open dropdown after page load
        sessionStorage.setItem('openDocumentsDropdown', 'true');
    } else {
        // Second click - close dropdown
        dropdown.classList.remove('show');
        documentsDropdownOpen = false;
        sessionStorage.removeItem('openDocumentsDropdown');
    }
}

function toggleFinancialDropdown() {
    const dropdown = document.getElementById('financialDropdown');
    
    if (!financialDropdownOpen) {
        // First click - go to all financial and open dropdown
        window.location.href = '{{ route("financial.index") }}';
        
        // Store state to open dropdown after page load
        sessionStorage.setItem('openFinancialDropdown', 'true');
    } else {
        // Second click - close dropdown
        dropdown.classList.remove('show');
        financialDropdownOpen = false;
        sessionStorage.removeItem('openFinancialDropdown');
    }
}

// Check if dropdown should be opened on page load
document.addEventListener('DOMContentLoaded', function() {
    // Open documents dropdown if requested
    if (sessionStorage.getItem('openDocumentsDropdown') === 'true') {
        const dropdown = document.getElementById('documentsDropdown');
        dropdown.classList.add('show');
        documentsDropdownOpen = true;
        sessionStorage.removeItem('openDocumentsDropdown');
    }
    
    // Open financial dropdown if requested
    if (sessionStorage.getItem('openFinancialDropdown') === 'true') {
        const dropdown = document.getElementById('financialDropdown');
        dropdown.classList.add('show');
        financialDropdownOpen = true;
        sessionStorage.removeItem('openFinancialDropdown');
    }
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.nav-dropdown')) {
        // Close documents dropdown
        const documentsDropdown = document.getElementById('documentsDropdown');
        documentsDropdown.classList.remove('show');
        documentsDropdownOpen = false;
        
        // Close financial dropdown
        const financialDropdown = document.getElementById('financialDropdown');
        financialDropdown.classList.remove('show');
        financialDropdownOpen = false;
    }
});
</script>
