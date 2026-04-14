<aside class="sidebar">
    <!-- Navigation starts here -->

    <!-- Profile Section -->
    <div class="profile-section sidebar-profile-card">
        <div class="profile-avatar sidebar-profile-avatar">
            @if(auth()->user() && auth()->user()->profile_photo_path)
                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Profile" class="sidebar-profile-image">
            @else
                <div class="profile-avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
            @endif
        </div>
        <div class="profile-info">
            <div class="profile-name sidebar-profile-name">{{ auth()->user()->name ?? 'JOHN DOE' }}</div>
            <div class="profile-role sidebar-profile-role">{{ auth()->user()->role ?? 'ADMINISTRATOR' }}</div>
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
            <i class="fas fa-list"></i> All Documents <i class="fas fa-chevron-down sidebar-nav-chevron"></i>
        </a>
        <div class="nav-sub" id="documentsDropdown">
            <a href="{{ route('documents.index', ['direction' => 'INCOMING']) }}" class="nav-item nav-item--compact {{ request()->routeIs('documents.index') && request('direction') === 'INCOMING' ? 'active' : '' }}">
                <i class="fas fa-arrow-down"></i> Incoming Documents
            </a>
            <a href="{{ route('documents.index', ['direction' => 'OUTGOING']) }}" class="nav-item nav-item--compact {{ request()->routeIs('documents.index') && request('direction') === 'OUTGOING' ? 'active' : '' }}">
                <i class="fas fa-arrow-up"></i> Outgoing Documents
            </a>
        </div>
    </div>

    <!-- Financial Dropdown -->
    <div class="nav-dropdown">
        <a href="{{ route('financial.index') }}" class="nav-item {{ request()->routeIs('financial.index') && !request()->has('status') ? 'active' : '' }}" onclick="toggleFinancialDropdown()">
            <i class="fas fa-coins"></i> All Financial <i class="fas fa-chevron-down sidebar-nav-chevron"></i>
        </a>
        <div class="nav-sub" id="financialDropdown">
            <a href="{{ route('financial.index', ['status' => 'ACTIVE']) }}" class="nav-item {{ request()->routeIs('financial.index') && request('status') === 'ACTIVE' ? 'active' : '' }}">
                <i class="fas fa-play-circle"></i> Active
            </a>
            <a href="{{ route('financial.index', ['status' => 'CANCELLED']) }}" class="nav-item {{ request()->routeIs('financial.index') && request('status') === 'CANCELLED' ? 'active' : '' }}">
                <i class="fas fa-times-circle"></i> Cancelled
            </a>
            <a href="{{ route('financial.index', ['status' => 'FINISHED']) }}" class="nav-item {{ request()->routeIs('financial.index') && request('status') === 'FINISHED' ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> Finished
            </a>
        </div>
    </div>

    <!-- Spacer to push User Management to bottom -->
    <div style="flex: 1;"></div>

    @if(auth()->user() && auth()->user()->isAdmin())
    <!-- User Management (Admin Only) - Absolute Bottom -->
    <div class="nav-section sidebar-admin-section">
        <div class="sidebar-admin-links">
            <a href="{{ route('users.index') }}" class="nav-item sidebar-admin-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> User Management
            </a>
            <a href="{{ route('offices.index') }}" class="nav-item sidebar-admin-link {{ request()->routeIs('offices.*') ? 'active' : '' }}">
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

</script>
