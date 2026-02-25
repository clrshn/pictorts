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
            <i class="fas fa-list"></i> All Documents
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
            <i class="fas fa-coins"></i> All Financial
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

    <!-- Quick Actions (Hidden but accessible) -->
    <div class="nav-section" style="margin-top: 20px;">
        <button class="nav-item quick-action" onclick="window.location.href='{{ route('documents.create') }}'" style="width: 100%; justify-content: center; background: #27ae60; color: white; border-radius: 6px; margin-bottom: 8px;">
            <i class="fas fa-plus"></i> New Document
        </button>
        <button class="nav-item quick-action" onclick="window.location.href='{{ route('financial.create') }}'" style="width: 100%; justify-content: center; background: #e67e22; color: white; border-radius: 6px;">
            <i class="fas fa-plus"></i> New Financial
        </button>
    </div>
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
