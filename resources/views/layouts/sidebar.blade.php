<aside class="sidebar">
    <!-- Logo -->
    <div class="logo-area">
        <div style="width:44px;height:44px;background:#c0392b;border-radius:8px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-file-alt" style="color:#fff;font-size:20px;"></i>
        </div>
        <div class="logo-text">
            <span>PICTO</span> RECORDS<br>& TRACKING<br><span>SYSTEM</span>
        </div>
    </div>

    <!-- Navigation -->
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <a href="{{ route('track.page') }}" class="nav-item {{ request()->routeIs('track.page') ? 'active' : '' }}">
        <i class="fas fa-qrcode"></i> Track Document
    </a>

    <a href="{{ route('todos.index') }}" class="nav-item {{ request()->routeIs('todos.*') ? 'active' : '' }}">
        <i class="fas fa-tasks"></i> To-Do
    </a>

    <!-- Documents Dropdown -->
    <div class="nav-dropdown">
        <button class="nav-dropdown-btn {{ request()->routeIs('documents.index') && !request()->has('direction') ? 'active' : '' }}" onclick="toggleDocumentsDropdown()">
            <div style="display: flex; align-items: center; flex: 1;">
                <i class="fas fa-list" style="width: 22px; margin-right: 10px; text-align: center;"></i>
                <span>All Documents</span>
            </div>
            <i class="fas fa-chevron-down dropdown-arrow" id="documentsArrow"></i>
        </button>
        <div class="nav-dropdown-menu" id="documentsDropdown">
            <a href="{{ route('documents.index', ['direction' => 'INCOMING']) }}" class="nav-dropdown-item">
                <i class="fas fa-arrow-down"></i> Incoming Documents
            </a>
            <a href="{{ route('documents.index', ['direction' => 'OUTGOING']) }}" class="nav-dropdown-item">
                <i class="fas fa-arrow-up"></i> Outgoing Documents
            </a>
        </div>
    </div>

    <!-- Financial Dropdown -->
    <div class="nav-dropdown">
        <button class="nav-dropdown-btn {{ request()->routeIs('financial.index') && !request()->has('status') ? 'active' : '' }}" onclick="toggleFinancialDropdown()">
            <div style="display: flex; align-items: center; flex: 1;">
                <i class="fas fa-coins" style="width: 22px; margin-right: 10px; text-align: center;"></i>
                <span>All Financial</span>
            </div>
            <i class="fas fa-chevron-down dropdown-arrow" id="financialArrow"></i>
        </button>
        <div class="nav-dropdown-menu" id="financialDropdown">
            <a href="{{ route('financial.index', ['status' => 'ACTIVE']) }}" class="nav-dropdown-item">
                <i class="fas fa-play-circle"></i> Active
            </a>
            <a href="{{ route('financial.index', ['status' => 'CANCELLED']) }}" class="nav-dropdown-item">
                <i class="fas fa-times-circle"></i> Cancelled
            </a>
            <a href="{{ route('financial.index', ['status' => 'FINISHED']) }}" class="nav-dropdown-item">
                <i class="fas fa-check-circle"></i> Finished
            </a>
        </div>
    </div>

    <!-- Spacer to push User Management to bottom -->
    <div style="flex: 1;"></div>

    @if(auth()->user() && auth()->user()->isAdmin())
    <!-- User Management (Admin Only) - Absolute Bottom -->
    <div class="nav-section" style="text-align: center; padding: 10px 0; margin: 0; position: absolute; bottom: 0; left: 0; right: 0;">
        <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" style="display: inline-flex; justify-content: center; width: auto; padding: 12px 20px;">
            <i class="fas fa-users"></i> User Management
        </a>
    </div>
    @endif
</aside>

<script>
let documentsDropdownOpen = false;
let financialDropdownOpen = false;

function toggleDocumentsDropdown() {
    const dropdown = document.getElementById('documentsDropdown');
    const arrow = document.getElementById('documentsArrow');
    
    if (!documentsDropdownOpen) {
        // First click - go to all documents and open dropdown
        window.location.href = '{{ route("documents.index") }}';
        
        // Store state to open dropdown after page load
        sessionStorage.setItem('openDocumentsDropdown', 'true');
    } else {
        // Second click - close dropdown
        dropdown.classList.remove('show');
        arrow.style.transform = 'rotate(0deg)';
        documentsDropdownOpen = false;
        sessionStorage.removeItem('openDocumentsDropdown');
    }
}

function toggleFinancialDropdown() {
    const dropdown = document.getElementById('financialDropdown');
    const arrow = document.getElementById('financialArrow');
    
    if (!financialDropdownOpen) {
        // First click - go to all financial and open dropdown
        window.location.href = '{{ route("financial.index") }}';
        
        // Store state to open dropdown after page load
        sessionStorage.setItem('openFinancialDropdown', 'true');
    } else {
        // Second click - close dropdown
        dropdown.classList.remove('show');
        arrow.style.transform = 'rotate(0deg)';
        financialDropdownOpen = false;
        sessionStorage.removeItem('openFinancialDropdown');
    }
}

// Check if dropdown should be opened on page load
document.addEventListener('DOMContentLoaded', function() {
    // Open documents dropdown if requested
    if (sessionStorage.getItem('openDocumentsDropdown') === 'true') {
        const dropdown = document.getElementById('documentsDropdown');
        const arrow = document.getElementById('documentsArrow');
        dropdown.classList.add('show');
        arrow.style.transform = 'rotate(180deg)';
        documentsDropdownOpen = true;
        sessionStorage.removeItem('openDocumentsDropdown');
    }
    
    // Open financial dropdown if requested
    if (sessionStorage.getItem('openFinancialDropdown') === 'true') {
        const dropdown = document.getElementById('financialDropdown');
        const arrow = document.getElementById('financialArrow');
        dropdown.classList.add('show');
        arrow.style.transform = 'rotate(180deg)';
        financialDropdownOpen = true;
        sessionStorage.removeItem('openFinancialDropdown');
    }
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.nav-dropdown')) {
        // Close documents dropdown
        const documentsDropdown = document.getElementById('documentsDropdown');
        const documentsArrow = document.getElementById('documentsArrow');
        documentsDropdown.classList.remove('show');
        documentsArrow.style.transform = 'rotate(0deg)';
        documentsDropdownOpen = false;
        
        // Close financial dropdown
        const financialDropdown = document.getElementById('financialDropdown');
        const financialArrow = document.getElementById('financialArrow');
        financialDropdown.classList.remove('show');
        financialArrow.style.transform = 'rotate(0deg)';
        financialDropdownOpen = false;
    }
});
</script>
