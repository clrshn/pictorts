<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>PICTO-RTS</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/@zxing/library@latest"></script>

        <style>
            /* Sidebar */
            .sidebar { 
                width: 250px; 
                min-height: 100vh; 
                background: #1a1a2e; 
                position: fixed; 
                left: 0; 
                top: 0; 
                z-index: 40; 
                transition: all 0.3s; 
                overflow-y: auto;
                visibility: visible !important;
                opacity: 1 !important;
                transform: translateX(0) !important;
            }
            .sidebar.collapsed { transform: translateX(-100%); }
            .sidebar .logo-area { background: #16213e; padding: 16px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.08); }
            .sidebar .logo-area img { width: 44px; height: 44px; }
            .sidebar .logo-area .logo-text { color: #fff; font-weight: 700; font-size: 14px; line-height: 1.2; }
            .sidebar .logo-area .logo-text span { color: #c0392b; }
            .sidebar .nav-item { display: block; padding: 12px 20px; color: rgba(255,255,255,0.7); font-size: 14px; text-decoration: none; transition: all 0.2s; border-left: 3px solid transparent; }
            .sidebar .nav-item:hover { background: rgba(255,255,255,0.05); color: #fff; }
            .sidebar .nav-item.active { background: rgba(192,57,43,0.15); color: #fff; border-left-color: #c0392b; }
            .sidebar .nav-item i { width: 22px; margin-right: 10px; text-align: center; }
            .sidebar .nav-section { padding: 10px 20px 5px; font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.35); letter-spacing: 1px; margin-top: 5px; }
            .sidebar .nav-sub { display: none; background: rgba(0,0,0,0.15); }
            .sidebar .nav-sub.show { display: block; }
            .sidebar .nav-sub .nav-item { padding-left: 52px; font-size: 13px; }

            /* Main Content */
            .main-content { 
                margin-left: 250px; 
                min-height: 100vh; 
                background: #f4f6f9; 
                transition: all 0.3s; 
                display: flex; 
                flex-direction: column;
            }
            .main-content.expanded { margin-left: 0; }
            .top-bar { background: #fff; padding: 0 24px; height: 56px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
            .top-bar .close-btn { font-size: 20px; color: #666; cursor: pointer; }
            .top-bar .user-area { display: flex; align-items: center; gap: 10px; }
            .top-bar .user-area .user-btn { background: #c0392b; color: #fff; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer; border: none; }
            .top-bar .user-area .user-btn .avatar { width: 28px; height: 28px; border-radius: 50%; background: #e74c3c; display: flex; align-items: center; justify-content: center; font-size: 12px; }

            .page-header { padding: 20px 24px 0; }
            .page-header h1 { font-size: 22px; font-weight: 700; color: #2d3436; margin: 0; }
            .page-header .breadcrumb { font-size: 13px; color: #999; margin-top: 2px; }
            .page-header .breadcrumb a { color: #c0392b; text-decoration: none; }

            .content-body { padding: 20px 24px; flex: 1; }
            .content-wrapper { flex: 1; display: flex; flex-direction: column; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; background: #fff; border-top: 1px solid #e5e7eb; margin-top: auto; }
            .footer a { color: #c0392b; text-decoration: none; }

            /* Cards */
            .stat-card { border-radius: 8px; padding: 20px 24px; color: #fff; display: flex; align-items: center; justify-content: space-between; min-height: 80px; }
            .stat-card .icon { font-size: 32px; opacity: 0.8; }
            .stat-card .info .label { font-size: 14px; font-weight: 600; }
            .stat-card .info .count { font-size: 28px; font-weight: 700; }
            .stat-card.yellow { background: linear-gradient(135deg, #f1c40f, #f4d03f); }
            .stat-card.orange { background: linear-gradient(135deg, #e67e22, #f39c12); }
            .stat-card.green { background: linear-gradient(135deg, #27ae60, #2ecc71); }
            .stat-card.teal { background: linear-gradient(135deg, #00b894, #00cec9); }
            .stat-card.blue { background: linear-gradient(135deg, #2980b9, #3498db); }
            .stat-card.red { background: linear-gradient(135deg, #c0392b, #e74c3c); }
            .stat-card.indigo { background: linear-gradient(135deg, #6c5ce7, #a29bfe); }

            /* Filter box */
            .filter-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; }
            .filter-box h3 { font-size: 14px; font-weight: 700; color: #2d3436; margin-bottom: 12px; border-bottom: 2px solid #c0392b; padding-bottom: 6px; display: inline-block; }

            /* Table card */
            .table-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
            .table-card .table-header { padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
            .table-card .table-header h3 { font-size: 16px; font-weight: 700; color: #2d3436; margin: 0; }
            .table-card table { width: 100%; border-collapse: collapse; }
            .table-card table thead th { background: #8b0000; color: #fff; padding: 10px 14px; font-size: 12px; font-weight: 600; text-transform: uppercase; text-align: center; letter-spacing: 0.5px; }
            .table-card table tbody td { padding: 10px 14px; font-size: 13px; color: #444; border-bottom: 1px solid #f0f0f0; text-align: center; vertical-align: middle; }
            .table-card table tbody tr:hover { background: #fafbfc; }

            /* Buttons */
            .btn-red { background: #c0392b; color: #fff; padding: 8px 18px; border-radius: 4px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
            .btn-red:hover { background: #a93226; color: #fff; }
            .btn-blue { background: #2980b9; color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 12px; border: none; cursor: pointer; text-decoration: none; }
            .btn-blue:hover { background: #2471a3; color: #fff; }
            .btn-orange { background: #e67e22; color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 12px; border: none; cursor: pointer; text-decoration: none; }
            .btn-danger { background: #e74c3c; color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 12px; border: none; cursor: pointer; text-decoration: none; }
            .btn-green { background: #27ae60; color: #fff; padding: 8px 18px; border-radius: 4px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; }
            .btn-gray { background: #95a5a6; color: #fff; padding: 8px 18px; border-radius: 4px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; }

            /* Badge */
            .badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-block; }
            .badge-ongoing { background: #e67e22; color: #fff; }
            .badge-delivered { background: #2980b9; color: #fff; }
            .badge-completed { background: #27ae60; color: #fff; }
            .badge-active { background: #27ae60; color: #fff; }
            .badge-cancelled { background: #e74c3c; color: #fff; }
            .badge-finished { background: #00b894; color: #fff; }
            .badge-primary { background: #3498db; color: #fff; }
            .badge-danger { background: #e74c3c; color: #fff; }

            /* Alerts */
            .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid; }
            .alert-success { background: #d4edda; border-color: #27ae60; color: #155724; }
            .alert-danger { background: #f8d7da; border-color: #e74c3c; color: #721c24; }
            .alert-warning { background: #fff3cd; border-color: #f39c12; color: #856404; }
            .alert-info { background: #d1ecf1; border-color: #3498db; color: #0c5460; }

            /* Search input */
            .search-input { border: 1px solid #ddd; border-radius: 4px; padding: 8px 14px; font-size: 13px; outline: none; }
            .search-input:focus { border-color: #c0392b; }

            /* Form controls */
            .form-group { margin-bottom: 16px; }
            .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 5px; }
            .form-control { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 8px 12px; font-size: 13px; outline: none; background: #fff; }
            .form-control:focus { border-color: #c0392b; box-shadow: 0 0 0 2px rgba(192,57,43,0.1); }
            select.form-control { appearance: auto; }

            /* Footer */
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; }
            .footer a { color: #c0392b; text-decoration: none; }

            /* Alert */
            .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
            .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }

            /* Dropdown */
            .user-dropdown { position: relative; }
            .user-dropdown-menu { display: none; position: absolute; right: 0; top: 100%; background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 160px; z-index: 50; margin-top: 4px; }
            .user-dropdown-menu.show { display: block; }
            .user-dropdown-menu a, .user-dropdown-menu button { display: block; width: 100%; text-align: left; padding: 8px 16px; font-size: 13px; color: #444; border: none; background: none; cursor: pointer; text-decoration: none; }
            .user-dropdown-menu a:hover, .user-dropdown-menu button:hover { background: #f5f5f5; }

            /* Sidebar Dropdowns */
            .nav-dropdown { margin-bottom: 4px; }
            .nav-dropdown-btn { 
                display: flex; 
                align-items: center; 
                justify-content: space-between; 
                width: 100%; 
                padding: 12px 20px; 
                color: #666; 
                text-decoration: none; 
                font-size: 14px; 
                font-weight: 500; 
                border: none; 
                background: none; 
                cursor: pointer; 
                transition: all 0.2s;
            }
            .nav-dropdown-btn:hover { background: #f8f9fa; color: #c0392b; }
            .nav-dropdown-btn.active { background: #c0392b; color: #fff; }
            .dropdown-arrow { 
                font-size: 10px; 
                transition: transform 0.2s;
            }
            .nav-dropdown-menu { 
                display: none; 
                background: #f8f9fa; 
                border-left: 3px solid #c0392b; 
                margin-left: 20px; 
                overflow: hidden;
            }
            .nav-dropdown-menu.show { display: block; }
            .nav-dropdown-item { 
                display: flex; 
                align-items: center; 
                padding: 10px 20px 10px 30px; 
                color: #666; 
                text-decoration: none; 
                font-size: 13px; 
                transition: all 0.2s;
                border-left: 2px solid transparent;
            }
            .nav-dropdown-item:hover { 
                background: #e9ecef; 
                color: #c0392b; 
                border-left-color: #c0392b;
            }
            .nav-dropdown-item i { 
                margin-right: 8px; 
                width: 14px; 
                text-align: center;
            }
            .quick-action:hover { 
                transform: translateY(-1px); 
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }

            /* Responsive */
            @media (max-width: 768px) {
                .sidebar { transform: translateX(-100%); }
                .sidebar.open { transform: translateX(0); }
                .main-content { margin-left: 0; }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div>
                    <span class="close-btn" onclick="toggleSidebar()">&times;</span>
                </div>
                <div class="user-area">
                    <div class="user-dropdown" id="userDropdown">
                        <button class="user-btn" id="userDropdownBtn" type="button" onclick="document.getElementById('userDropdownMenu').classList.toggle('show')">
                            <span class="avatar"><i class="fas fa-user"></i></span>
                            {{ Auth::user()->name ?? 'User' }} - PICTO
                            <i class="fas fa-chevron-down" style="font-size:10px"></i>
                        </button>
                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <a href="{{ route('profile.edit') }}"><i class="fas fa-cog" style="margin-right:6px;"></i> Settings</a>
                            <button type="button" onclick="document.getElementById('logoutModal').style.display='flex'; document.getElementById('userDropdownMenu').classList.remove('show');"><i class="fas fa-sign-out-alt" style="margin-right:6px;"></i> Log Out</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Page Header -->
                @isset($header)
                    <div class="page-header">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Page Content -->
                <div class="content-body">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                &copy; Copyright {{ date('Y') }}. All right reserved. <a href="#">PICTO Records & Tracking System</a>.
            </div>
        </div>

        <!-- Logout Confirmation Modal -->
        <div id="logoutModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:12px; padding:36px 40px; max-width:400px; width:90%; text-align:center; box-shadow:0 10px 40px rgba(0,0,0,0.2); animation: modalIn 0.25s ease;">
                <div style="width:70px; height:70px; border-radius:50%; border:3px solid #f39c12; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                    <i class="fas fa-exclamation" style="font-size:32px; color:#f39c12;"></i>
                </div>
                <h3 style="font-size:20px; font-weight:600; color:#333; margin-bottom:8px;">Are you sure you want to logout?</h3>
                <div style="margin-top:24px; display:flex; justify-content:center; gap:12px;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="padding:10px 28px; background:#2980b9; color:#fff; border:none; border-radius:4px; font-size:14px; font-weight:600; cursor:pointer;">Yes</button>
                    </form>
                    <button type="button" onclick="document.getElementById('logoutModal').style.display='none'" style="padding:10px 28px; background:#e74c3c; color:#fff; border:none; border-radius:4px; font-size:14px; font-weight:600; cursor:pointer;">Cancel</button>
                </div>
            </div>
        </div>
        <script>
            // Ensure sidebar is visible on page load
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');
                const closeBtn = document.querySelector('.close-btn');
                
                // Force sidebar to be open on page load
                if (sidebar && mainContent) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    if (closeBtn) {
                        closeBtn.textContent = '×'; // Close X
                    }
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                var dropdown = document.getElementById('userDropdown');
                var menu = document.getElementById('userDropdownMenu');
                if (dropdown && menu && !dropdown.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });

            // Close logout modal when clicking outside the box
            document.getElementById('logoutModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });

            // Toggle Sidebar
            function toggleSidebar() {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');
                const closeBtn = document.querySelector('.close-btn');
                
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Change button text
                if (sidebar.classList.contains('collapsed')) {
                    closeBtn.textContent = '☰'; // Hamburger menu
                } else {
                    closeBtn.textContent = '×'; // Close X
                }
            }
        </script>
    </body>
</html>
