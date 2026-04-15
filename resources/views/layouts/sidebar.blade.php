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
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" onclick="setSidebarSection('dashboard')">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <a href="{{ route('todos.index') }}" class="nav-item {{ request()->routeIs('todos.*') ? 'active' : '' }}" onclick="setSidebarSection('todos')">
        <i class="fas fa-tasks"></i> To-Do
    </a>

    <!-- Documents Dropdown -->
    <div class="nav-dropdown">
        <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.index') && !request()->has('direction') && !request()->has('delivery_scope') ? 'active' : '' }}" onclick="toggleDocumentsDropdown(event)">
            <i class="fas fa-list"></i> All Documents <i class="fas fa-chevron-down sidebar-nav-chevron"></i>
        </a>
        <div class="nav-sub" id="documentsDropdown">
            <a href="{{ route('documents.index', ['direction' => 'INCOMING']) }}" class="nav-item nav-item--compact {{ request()->routeIs('documents.index') && request('direction') === 'INCOMING' ? 'active' : '' }}" onclick="setSidebarSection('documents')">
                <i class="fas fa-arrow-down"></i> Incoming Documents
            </a>
            <a href="{{ route('documents.index', ['direction' => 'OUTGOING']) }}" class="nav-item nav-item--compact {{ request()->routeIs('documents.index') && request('direction') === 'OUTGOING' && !request('delivery_scope') && request('type') !== 'TO' ? 'active' : '' }}" onclick="toggleOutgoingDocumentsDropdown(event)">
                <i class="fas fa-arrow-up"></i> Outgoing Documents <i class="fas fa-chevron-down sidebar-nav-chevron"></i>
            </a>
            <div class="nav-sub nav-sub--nested" id="outgoingDocumentsDropdown">
                <a href="{{ route('documents.index', ['direction' => 'OUTGOING', 'delivery_scope' => 'EXTERNAL']) }}" class="nav-item nav-item--compact nav-item--nested {{ request()->routeIs('documents.index') && request('direction') === 'OUTGOING' && request('delivery_scope') === 'EXTERNAL' ? 'active' : '' }}" onclick="setSidebarSection('documents')">
                    External
                </a>
                <a href="{{ route('documents.index', ['direction' => 'OUTGOING', 'delivery_scope' => 'INTERNAL']) }}" class="nav-item nav-item--compact nav-item--nested {{ request()->routeIs('documents.index') && request('direction') === 'OUTGOING' && request('delivery_scope') === 'INTERNAL' ? 'active' : '' }}" onclick="setSidebarSection('documents')">
                    Internal
                </a>
                <a href="{{ route('documents.index', ['type' => 'TO']) }}" class="nav-item nav-item--compact nav-item--nested {{ request()->routeIs('documents.index') && request('type') === 'TO' && !request('travel_order_type') ? 'active' : '' }}" onclick="toggleTravelOrdersDropdown(event)">
                    <i class="fas fa-route"></i> Travel Orders <i class="fas fa-chevron-down sidebar-nav-chevron"></i>
                </a>
                <div class="nav-sub nav-sub--nested-deeper" id="travelOrdersDropdown">
                    <a href="{{ route('documents.index', ['type' => 'TO', 'travel_order_type' => 'WITHIN_LA_UNION']) }}" class="nav-item nav-item--compact nav-item--nested nav-item--nested-deeper {{ request()->routeIs('documents.index') && request('type') === 'TO' && request('travel_order_type') === 'WITHIN_LA_UNION' ? 'active' : '' }}" onclick="setSidebarSection('documents')">
                        Within La Union
                    </a>
                    <a href="{{ route('documents.index', ['type' => 'TO', 'travel_order_type' => 'OUTSIDE_LA_UNION']) }}" class="nav-item nav-item--compact nav-item--nested nav-item--nested-deeper {{ request()->routeIs('documents.index') && request('type') === 'TO' && request('travel_order_type') === 'OUTSIDE_LA_UNION' ? 'active' : '' }}" onclick="setSidebarSection('documents')">
                        Outside La Union
                    </a>
                    <a href="{{ route('documents.index', ['type' => 'TO', 'travel_order_type' => 'SPECIAL_ORDER']) }}" class="nav-item nav-item--compact nav-item--nested nav-item--nested-deeper {{ request()->routeIs('documents.index') && request('type') === 'TO' && request('travel_order_type') === 'SPECIAL_ORDER' ? 'active' : '' }}" onclick="setSidebarSection('documents')">
                        Special Order
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Dropdown -->
    <div class="nav-dropdown">
        <a href="{{ route('financial.index') }}" class="nav-item {{ request()->routeIs('financial.index') && !request()->has('status') ? 'active' : '' }}" onclick="toggleFinancialDropdown(event)">
            <i class="fas fa-coins"></i> All Financial <i class="fas fa-chevron-down sidebar-nav-chevron"></i>
        </a>
        <div class="nav-sub" id="financialDropdown">
            <a href="{{ route('financial.index', ['status' => 'ACTIVE']) }}" class="nav-item {{ request()->routeIs('financial.index') && request('status') === 'ACTIVE' ? 'active' : '' }}" onclick="setSidebarSection('financial')">
                <i class="fas fa-play-circle"></i> Active
            </a>
            <a href="{{ route('financial.index', ['status' => 'CANCELLED']) }}" class="nav-item {{ request()->routeIs('financial.index') && request('status') === 'CANCELLED' ? 'active' : '' }}" onclick="setSidebarSection('financial')">
                <i class="fas fa-times-circle"></i> Cancelled
            </a>
            <a href="{{ route('financial.index', ['status' => 'FINISHED']) }}" class="nav-item {{ request()->routeIs('financial.index') && request('status') === 'FINISHED' ? 'active' : '' }}" onclick="setSidebarSection('financial')">
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
            <a href="{{ route('users.index') }}" class="nav-item sidebar-admin-link {{ request()->routeIs('users.*') ? 'active' : '' }}" onclick="setSidebarSection('users')">
                <i class="fas fa-users"></i> User Management
            </a>
            <a href="{{ route('offices.index') }}" class="nav-item sidebar-admin-link {{ request()->routeIs('offices.*') ? 'active' : '' }}" onclick="setSidebarSection('offices')">
                <i class="fas fa-building"></i> Office Management
            </a>
        </div>
    </div>
    @endif
</aside>

<script>
let documentsDropdownOpen = false;
let outgoingDocumentsDropdownOpen = false;
let travelOrdersDropdownOpen = false;
let financialDropdownOpen = false;

function setSidebarSection(section) {
    sessionStorage.setItem('activeSidebarSection', section);
}

function openDocumentsDropdown() {
    const dropdown = document.getElementById('documentsDropdown');
    dropdown.classList.add('show');
    documentsDropdownOpen = true;
}

function closeDocumentsDropdown() {
    const dropdown = document.getElementById('documentsDropdown');
    dropdown.classList.remove('show');
    documentsDropdownOpen = false;
    closeOutgoingDocumentsDropdown();
    closeTravelOrdersDropdown();
}

function openFinancialDropdown() {
    const dropdown = document.getElementById('financialDropdown');
    dropdown.classList.add('show');
    financialDropdownOpen = true;
}

function closeFinancialDropdown() {
    const dropdown = document.getElementById('financialDropdown');
    dropdown.classList.remove('show');
    financialDropdownOpen = false;
}

function openOutgoingDocumentsDropdown() {
    const dropdown = document.getElementById('outgoingDocumentsDropdown');
    dropdown.classList.add('show');
    outgoingDocumentsDropdownOpen = true;
}

function closeOutgoingDocumentsDropdown() {
    const dropdown = document.getElementById('outgoingDocumentsDropdown');
    dropdown.classList.remove('show');
    outgoingDocumentsDropdownOpen = false;
    closeTravelOrdersDropdown();
}

function openTravelOrdersDropdown() {
    const dropdown = document.getElementById('travelOrdersDropdown');
    dropdown.classList.add('show');
    travelOrdersDropdownOpen = true;
}

function closeTravelOrdersDropdown() {
    const dropdown = document.getElementById('travelOrdersDropdown');
    dropdown.classList.remove('show');
    travelOrdersDropdownOpen = false;
}

function toggleDocumentsDropdown(event) {
    event.preventDefault();
    setSidebarSection('documents');

    if (documentsDropdownOpen) {
        closeDocumentsDropdown();
        sessionStorage.removeItem('activeSidebarSection');
        return;
    }

    openDocumentsDropdown();
    closeFinancialDropdown();
    window.location.href = '{{ route("documents.index") }}';
}

function toggleOutgoingDocumentsDropdown(event) {
    event.preventDefault();
    event.stopPropagation();
    setSidebarSection('documents');

    if (!documentsDropdownOpen) {
        openDocumentsDropdown();
    }

    if (outgoingDocumentsDropdownOpen) {
        closeOutgoingDocumentsDropdown();
        closeTravelOrdersDropdown();
        return;
    }

    openOutgoingDocumentsDropdown();
    window.location.href = '{{ route("documents.index", ['direction' => 'OUTGOING']) }}';
}

function toggleTravelOrdersDropdown(event) {
    event.preventDefault();
    event.stopPropagation();
    setSidebarSection('documents');

    if (!documentsDropdownOpen) {
        openDocumentsDropdown();
    }

    if (!outgoingDocumentsDropdownOpen) {
        openOutgoingDocumentsDropdown();
    }

    if (travelOrdersDropdownOpen) {
        closeTravelOrdersDropdown();
        return;
    }

    openTravelOrdersDropdown();
    window.location.href = '{{ route("documents.index", ['type' => 'TO']) }}';
}

function toggleFinancialDropdown(event) {
    event.preventDefault();
    setSidebarSection('financial');

    if (financialDropdownOpen) {
        closeFinancialDropdown();
        sessionStorage.removeItem('activeSidebarSection');
        return;
    }

    openFinancialDropdown();
    closeDocumentsDropdown();
    window.location.href = '{{ route("financial.index") }}';
}

document.addEventListener('DOMContentLoaded', function() {
    const activeSection = sessionStorage.getItem('activeSidebarSection');
    const isDocumentsPage = {{ request()->routeIs('documents.*') ? 'true' : 'false' }};
    const isOutgoingDocumentsPage = {{ request()->routeIs('documents.index') && request('direction') === 'OUTGOING' ? 'true' : 'false' }};
    const isTravelOrdersPage = {{ request()->routeIs('documents.index') && request('type') === 'TO' ? 'true' : 'false' }};
    const isFinancialPage = {{ request()->routeIs('financial.*') ? 'true' : 'false' }};

    if (isDocumentsPage || activeSection === 'documents') {
        openDocumentsDropdown();
    }

    if (isOutgoingDocumentsPage || isTravelOrdersPage) {
        openOutgoingDocumentsDropdown();
    }

    if (isTravelOrdersPage) {
        openTravelOrdersDropdown();
    }

    if (isFinancialPage || activeSection === 'financial') {
        openFinancialDropdown();
    }

    if (activeSection && activeSection !== 'documents') {
        closeDocumentsDropdown();
    }

    if (activeSection && activeSection !== 'financial') {
        closeFinancialDropdown();
    }
});

</script>
