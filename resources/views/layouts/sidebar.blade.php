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

    <div class="nav-section">Transactions</div>

    <a href="{{ route('documents.create') }}" class="nav-item {{ request()->routeIs('documents.create') ? 'active' : '' }}">
        <i class="fas fa-plus-circle"></i> Encode Document
    </a>

    <a href="{{ route('financial.create') }}" class="nav-item {{ request()->routeIs('financial.create') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i> Encode Financial
    </a>

    <div class="nav-section">Documents</div>

    <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.index') ? 'active' : '' }}">
        <i class="fas fa-list"></i> All Documents
    </a>

    <div class="nav-section">Financial</div>

    <a href="{{ route('financial.index') }}" class="nav-item {{ request()->routeIs('financial.index') ? 'active' : '' }}">
        <i class="fas fa-coins"></i> All Records
    </a>
</aside>
