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
            /* Custom PICTO-RTS Unique Design */
            
            /* Sidebar - Light Design */
            .sidebar { 
                width: 260px; 
                min-height: 100vh; 
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); 
                position: fixed; 
                top: 0; 
                left: 0; 
                z-index: 1000; 
                transition: all 0.3s ease; 
                box-shadow: 4px 0 15px rgba(0,0,0,0.15);
            }
            .sidebar.collapsed { 
                transform: translateX(-260px); 
                opacity: 0;
                visibility: hidden;
            }
            .sidebar .logo-area { 
                background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%); 
                padding: 20px; 
                display: flex; 
                align-items: center; 
                gap: 12px; 
                border-bottom: 2px solid rgba(255,255,255,0.1);
                position: relative;
            }
            .sidebar .logo-area::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(90deg, #2980b9, #c0392b, #2980b9);
                animation: shimmer 3s ease-in-out infinite;
            }
            @keyframes shimmer {
                0%, 100% { opacity: 0.6; }
                50% { opacity: 1; }
            }
            .sidebar .logo-area img { 
                width: 40px; 
                height: 40px; 
                border-radius: 50%;
                border: 2px solid rgba(255,255,255,0.2);
            }
            .sidebar .logo-area .logo-text { 
                color: #ffffff; 
                font-weight: 800; 
                font-size: 16px; 
                line-height: 1.2; 
                text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            }
            .sidebar .logo-area .logo-text span { 
                color: #ffffff; 
                font-weight: 900;
                background: linear-gradient(45deg, #ffffff, #e3f2fd);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .sidebar .nav-item { 
                display: block; 
                padding: 14px 24px; 
                color: #475569; 
                font-size: 14px; 
                text-decoration: none; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
                border-left: 4px solid transparent;
                position: relative;
                margin: 4px 8px;
                border-radius: 0 8px 8px 0;
            }
            .sidebar .nav-item::before {
                content: '';
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 4px;
                height: 0;
                background: linear-gradient(180deg, #2980b9, #c0392b);
                transition: height 0.3s ease;
            }
            .sidebar .nav-item:hover { 
                background: linear-gradient(90deg, rgba(192,57,43,0.08) 0%, rgba(192,57,43,0.15) 100%); 
                color: #c0392b; 
                transform: translateX(1px);
                border-left-color: #2980b9;
            }
            .sidebar .nav-item:hover::before {
                height: 100%;
            }
            .sidebar .nav-item.active { 
                background: linear-gradient(90deg, rgba(192,57,43,0.15) 0%, rgba(41,128,185,0.08) 100%); 
                color: #c0392b; 
                border-left-color: #c0392b;
                font-weight: 600;
                box-shadow: 0 4px 12px rgba(192,57,43,0.15);
            }
            .sidebar .nav-item.active::before {
                height: 100%;
                background: #c0392b;
            }
            .sidebar .nav-item i { 
                width: 24px; 
                margin-right: 12px; 
                text-align: center; 
                font-size: 16px;
                color: inherit;
            }
            .sidebar .nav-section { 
                padding: 12px 24px 8px; 
                font-size: 11px; 
                text-transform: uppercase; 
                color: #9ca3af; 
                letter-spacing: 2px; 
                margin-top: 16px;
                border-bottom: 1px solid rgba(192,57,43,0.1);
                position: relative;
            }
            .sidebar .nav-section::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 24px;
                right: 24px;
                height: 1px;
                background: linear-gradient(90deg, transparent, #2980b9, transparent);
                animation: slideGradient 4s ease-in-out infinite;
            }
            @keyframes slideGradient {
                0%, 100% { transform: translateX(-100%); }
                50% { transform: translateX(100%); }
            }
            .sidebar .nav-sub { 
                display: none; 
                background: rgba(192,57,43,0.05); 
                border-radius: 8px;
                margin: 4px 0;
            }
            .sidebar .nav-sub.show { display: block; }
            .sidebar .nav-sub .nav-item { 
                padding-left: 60px; 
                font-size: 13px; 
                margin: 2px 0;
                border-radius: 4px;
            }

            /* Main Content - Modern Design */
            .main-content { 
                margin-left: 260px; 
                min-height: 100vh; 
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); 
                transition: all 0.3s; 
                display: flex; 
                flex-direction: column;
            }
            .main-content.expanded { margin-left: 0; }
            .top-bar { 
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); 
                padding: 0 32px; 
                height: 64px; 
                display: flex; 
                align-items: center; 
                justify-content: space-between; 
                border-bottom: 1px solid rgba(192,57,43,0.1); 
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                backdrop-filter: blur(10px);
            }
            .top-bar .close-btn { 
                font-size: 20px; 
                color: #64748b; 
                cursor: pointer; 
                padding: 8px;
                border-radius: 8px;
                transition: all 0.2s ease;
            }
            .top-bar .close-btn:hover {
                background: rgba(192,57,43,0.1);
                color: #c0392b;
            }
            .top-bar .user-area { 
                display: flex; 
                align-items: center; 
                gap: 12px; 
            }
            .top-bar .user-area .user-btn { 
                background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%); 
                color: #ffffff; 
                padding: 10px 20px; 
                border-radius: 25px; 
                font-size: 13px; 
                font-weight: 700; 
                display: flex; 
                align-items: center; 
                gap: 8px; 
                cursor: pointer; 
                border: 2px solid rgba(255,255,255,0.2);
                box-shadow: 0 4px 12px rgba(192,57,43,0.4);
                transition: all 0.3s ease;
                min-height: 44px;
            }
            .top-bar .user-area .user-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(192,57,43,0.4);
            }
            .top-bar .user-area .user-btn .avatar { 
                width: 32px; 
                height: 32px; 
                border-radius: 50%; 
                background: linear-gradient(135deg, #2980b9 0%, #64b5f6 100%); 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                font-size: 14px;
                border: 2px solid rgba(255,255,255,0.3);
            }

            .page-header { 
                padding: 24px 32px 16px; 
                background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(192,57,43,0.1);
            }
            .page-header h1 { 
                font-size: 24px; 
                font-weight: 700; 
                color: #1a1a2e; 
                margin: 0; 
                text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            }
            .page-header .breadcrumb { 
                font-size: 13px; 
                color: #64748b; 
                margin-top: 4px; 
            }
            .page-header .breadcrumb a { 
                color: #c0392b; 
                text-decoration: none; 
                padding: 4px 8px; 
                border-radius: 6px; 
                transition: all 0.3s ease;
                border: 1px solid transparent;
                font-weight: 500;
            }
            .page-header .breadcrumb a:hover { 
                background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%); 
                color: #fff; 
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(192,57,43,0.3);
            }

            .content-body { 
                padding: 24px 32px; 
                flex: 1; 
            }
            .content-wrapper { 
                flex: 1; 
                display: flex; 
                flex-direction: column; 
            }
            .footer { 
                text-align: center; 
                padding: 24px; 
                font-size: 12px; 
                color: #64748b; 
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); 
                border-top: 1px solid rgba(192,57,43,0.1); 
                margin-top: auto; 
            }
            .footer a { 
                color: #c0392b; 
                text-decoration: none; 
                font-weight: 500;
            }

            /* Cards - Modern Design */
            .stat-card { 
                border-radius: 16px; 
                padding: 24px 28px; 
                color: #fff; 
                display: flex; 
                align-items: center; 
                justify-content: space-between; 
                min-height: 100px;
                position: relative;
                overflow: hidden;
                backdrop-filter: blur(10px);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .stat-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 70%);
                pointer-events: none;
            }
            .stat-card .icon { 
                font-size: 36px; 
                opacity: 0.9; 
                filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            }
            .stat-card .info .label { 
                font-size: 14px; 
                font-weight: 600; 
                text-transform: uppercase;
                letter-spacing: 1px;
                opacity: 0.9;
            }
            .stat-card .info .count { 
                font-size: 32px; 
                font-weight: 800; 
                text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }
            .stat-card.yellow { 
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); 
                box-shadow: 0 8px 24px rgba(245,158,11,0.3);
            }
            .stat-card.orange { 
                background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%); 
                box-shadow: 0 8px 24px rgba(234,88,12,0.3);
            }
            .stat-card.green { 
                background: linear-gradient(135deg, #16a34a 0%, #059669 100%); 
                box-shadow: 0 8px 24px rgba(22,163,74,0.3);
            }
            .stat-card.teal { 
                background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); 
                box-shadow: 0 8px 24px rgba(8,145,178,0.3);
            }
            .stat-card.blue { 
                background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); 
                box-shadow: 0 8px 24px rgba(37,99,235,0.3);
            }
            .stat-card.red { 
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); 
                box-shadow: 0 8px 24px rgba(220,38,38,0.3);
            }
            .stat-card.indigo { 
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
                box-shadow: 0 8px 24px rgba(79,70,237,0.3);
            }

            /* Filter box - Modern Design */
            .filter-box { 
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); 
                border: 1px solid rgba(192,57,43,0.1); 
                border-radius: 12px; 
                padding: 20px 24px; 
                margin-bottom: 24px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                backdrop-filter: blur(10px);
            }
            .filter-box h3 { 
                font-size: 15px; 
                font-weight: 700; 
                color: #1a1a2e; 
                margin-bottom: 16px; 
                border-bottom: 3px solid #c0392b; 
                padding-bottom: 8px; 
                display: inline-block;
                position: relative;
            }
            .filter-box h3::after {
                content: '';
                position: absolute;
                bottom: -3px;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, #2980b9, #c0392b, #2980b9);
                animation: shimmer 3s ease-in-out infinite;
            }

            /* Table card - Modern Design */
            .table-card { 
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); 
                border: 1px solid rgba(192,57,43,0.1); 
                border-radius: 12px; 
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                backdrop-filter: blur(10px);
            }
            .table-card .table-header { 
                padding: 20px 24px; 
                display: flex; 
                align-items: center; 
                justify-content: space-between; 
                flex-wrap: wrap; 
                gap: 12px;
                background: linear-gradient(135deg, rgba(192,57,43,0.05) 0%, rgba(41,128,185,0.02) 100%);
                border-bottom: 1px solid rgba(192,57,43,0.1);
            }
            .table-card .table-header h3 { 
                font-size: 18px; 
                font-weight: 700; 
                color: #1a1a2e; 
                margin: 0; 
                text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            }
            .table-card table { width: 100%; border-collapse: collapse; }
            .table-card table thead th { 
                background: linear-gradient(135deg, #1a1a2e 0%, #2d3748 100%); 
                color: #fff; 
                padding: 14px 16px; 
                font-size: 12px; 
                font-weight: 700; 
                text-transform: uppercase; 
                text-align: center; 
                letter-spacing: 1px;
                position: relative;
            }
            .table-card table thead th::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(90deg, #2980b9, #c0392b, #2980b9);
                animation: shimmer 3s ease-in-out infinite;
            }
            .table-card table tbody td { 
                padding: 14px 16px; 
                font-size: 13px; 
                color: #475569; 
                border-bottom: 1px solid rgba(192,57,43,0.05); 
                text-align: center; 
                vertical-align: middle;
                transition: all 0.2s ease;
            }
            .table-card table tbody tr { 
                transition: all 0.2s ease;
                position: relative;
            }
            .table-card table tbody tr:hover { 
                background: linear-gradient(90deg, rgba(41,128,185,0.05) 0%, rgba(192,57,43,0.02) 100%); 
                transform: scale(1.01);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            .table-card table tbody tr:hover td { 
                color: #1a1a2e; 
                font-weight: 500;
            }

            /* Buttons - Modern Design */
            .btn-red { 
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); 
                color: #fff; 
                padding: 10px 20px; 
                border-radius: 8px; 
                font-size: 13px; 
                font-weight: 600; 
                border: none; 
                cursor: pointer; 
                text-decoration: none; 
                display: inline-flex; 
                align-items: center; 
                gap: 8px; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(220,38,38,0.3);
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .btn-red:hover { 
                background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%); 
                color: #fff; 
                transform: translateY(-2px); 
                box-shadow: 0 6px 16px rgba(185,28,28,0.4);
            }
            .btn-blue { 
                background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); 
                color: #fff; 
                padding: 8px 16px; 
                border-radius: 6px; 
                font-size: 12px; 
                border: none; 
                cursor: pointer; 
                text-decoration: none; 
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(37,99,235,0.3);
            }
            .btn-blue:hover { 
                background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%); 
                color: #fff; 
                transform: translateY(-1px); 
                box-shadow: 0 4px 12px rgba(29,78,216,0.4);
            }
            .btn-orange { 
                background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%); 
                color: #fff; 
                padding: 8px 16px; 
                border-radius: 6px; 
                font-size: 12px; 
                border: none; 
                cursor: pointer; 
                text-decoration: none; 
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(234,88,12,0.3);
            }
            .btn-orange:hover { 
                background: linear-gradient(135deg, #c2410c 0%, #b91c1c 100%); 
                color: #fff; 
                transform: translateY(-1px); 
                box-shadow: 0 4px 12px rgba(194,65,28,0.4);
            }
            .btn-danger { 
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); 
                color: #fff; 
                padding: 8px 16px; 
                border-radius: 6px; 
                font-size: 12px; 
                border: none; 
                cursor: pointer; 
                text-decoration: none; 
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(220,38,38,0.3);
            }
            .btn-danger:hover { 
                background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%); 
                color: #fff; 
                transform: translateY(-1px); 
                box-shadow: 0 4px 12px rgba(185,28,28,0.4);
            }
            .btn-green { 
                background: linear-gradient(135deg, #16a34a 0%, #059669 100%); 
                color: #fff; 
                padding: 10px 20px; 
                border-radius: 8px; 
                font-size: 13px; 
                font-weight: 600; 
                border: none; 
                cursor: pointer; 
                text-decoration: none; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(22,163,74,0.3);
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .btn-green:hover { 
                background: linear-gradient(135deg, #15803d 0%, #047857 100%); 
                color: #fff; 
                transform: translateY(-2px); 
                box-shadow: 0 6px 16px rgba(21,128,87,0.4);
            }
            .btn-gray { 
                background: linear-gradient(135deg, #64748b 0%, #475569 100%); 
                color: #fff; 
                padding: 10px 20px; 
                border-radius: 8px; 
                font-size: 13px; 
                font-weight: 600; 
                border: none; 
                cursor: pointer; 
                text-decoration: none; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(100,116,139,0.3);
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .btn-gray:hover { 
                background: linear-gradient(135deg, #475569 0%, #334155 100%); 
                color: #fff; 
                transform: translateY(-2px); 
                box-shadow: 0 6px 16px rgba(71,85,105,0.4);
            }

            /* Badge - Modern Design */
            .badge { 
                padding: 6px 14px; 
                border-radius: 20px; 
                font-size: 11px; 
                font-weight: 700; 
                display: inline-block;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .badge-ongoing { background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%); color: #fff; }
            .badge-delivered { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #fff; }
            .badge-completed { background: linear-gradient(135deg, #16a34a 0%, #059669 100%); color: #fff; }
            .badge-active { background: linear-gradient(135deg, #16a34a 0%, #059669 100%); color: #fff; }
            .badge-cancelled { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #fff; }
            .badge-finished { background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); color: #fff; }
            .badge-primary { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #fff; }
            .badge-danger { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #fff; }

            /* Alerts - Modern Design */
            .alert { 
                padding: 16px 20px; 
                border-radius: 8px; 
                margin-bottom: 24px; 
                border-left: 4px solid; 
                backdrop-filter: blur(10px);
                position: relative;
            }
            .alert::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 70%);
                pointer-events: none;
            }
            .alert-success { 
                background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); 
                border-color: #16a34a; 
                color: #15803d; 
                box-shadow: 0 4px 12px rgba(22,163,74,0.2);
            }
            .alert-danger { 
                background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); 
                border-color: #dc2626; 
                color: #991b1b; 
                box-shadow: 0 4px 12px rgba(220,38,38,0.2);
            }
            .alert-warning { 
                background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); 
                border-color: #ea580c; 
                color: #c2410c; 
                box-shadow: 0 4px 12px rgba(234,88,12,0.2);
            }
            .alert-info { 
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); 
                border-color: #2563eb; 
                color: #1e40af; 
                box-shadow: 0 4px 12px rgba(37,99,235,0.2);
            }
            .alert-error { 
                background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); 
                border-color: #dc2626; 
                color: #991b1b; 
                box-shadow: 0 4px 12px rgba(220,38,38,0.2);
            }

            /* Form Elements - Modern Design */
            .form-group { 
                margin-bottom: 20px; 
                position: relative;
            }
            .form-group label { 
                display: block; 
                margin-bottom: 8px; 
                color: #374151; 
                font-size: 14px; 
                font-weight: 600; 
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .form-control { 
                width: 100%; 
                padding: 14px 18px; 
                border: 2px solid rgba(192,57,43,0.1); 
                border-radius: 8px; 
                font-size: 14px; 
                outline: none; 
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); 
                color: #1a1a2e; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                backdrop-filter: blur(10px);
            }
            .form-control:focus { 
                border-color: #c0392b; 
                box-shadow: 0 0 0 3px rgba(192,57,43,0.1), 0 4px 12px rgba(192,57,43,0.2);
                transform: translateY(-1px);
            }
            .form-control::placeholder { 
                color: #9ca3af; 
                font-style: italic;
            }

            /* Search input */
            .search-input { border: 1px solid #ddd; border-radius: 4px; padding: 8px 14px; font-size: 13px; outline: none; transition: all 0.2s ease; }
            .search-input:focus { border-color: #c0392b; }
            .search-input:hover { border-color: #007bff; border-width: 2px; box-shadow: 0 4px 8px rgba(0,123,255,0.3); transform: translateY(-2px); background: #f8f9ff; }

            /* Form controls */
            .form-group { margin-bottom: 16px; }
            .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 5px; }
            .form-control { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 8px 12px; font-size: 13px; outline: none; background: #fff; transition: all 0.2s ease; }
            .form-control:focus { border-color: #c0392b; box-shadow: 0 0 0 2px rgba(192,57,43,0.1); }
            .form-control:hover { border-color: #007bff; border-width: 2px; box-shadow: 0 4px 8px rgba(0,123,255,0.3); transform: translateY(-2px); background: #f8f9ff; }
            select.form-control { appearance: auto; }
            textarea.form-control { resize: vertical; min-height: 80px; }
            textarea.form-control:hover { border-color: #007bff; border-width: 2px; box-shadow: 0 4px 8px rgba(0,123,255,0.3); transform: translateY(-2px); background: #f8f9ff; }
            input[type="file"].form-control { cursor: pointer; }
            input[type="file"].form-control:hover { border-color: #007bff; border-width: 2px; box-shadow: 0 4px 8px rgba(0,123,255,0.3); transform: translateY(-2px); background: #f8f9ff; }
            input[type="date"].form-control:hover { border-color: #007bff; border-width: 2px; box-shadow: 0 4px 8px rgba(0,123,255,0.3); transform: translateY(-2px); background: #f8f9ff; }
            input[type="number"].form-control:hover { border-color: #007bff; border-width: 2px; box-shadow: 0 4px 8px rgba(0,123,255,0.3); transform: translateY(-2px); background: #f8f9ff; }
            input[type="email"].form-control:hover { border-color: #007bff; border-width: 2px; box-shadow: 0 4px 8px rgba(0,123,255,0.3); transform: translateY(-2px); background: #f8f9ff; }
            input[type="password"].form-control:hover { border-color: #007bff; border-width: 2px; box-shadow: 0 4px 8px rgba(0,123,255,0.3); transform: translateY(-2px); background: #f8f9ff; }

            /* Footer */
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; }
            .footer a { color: #c0392b; text-decoration: none; }

            /* Alert */
            .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
            .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }

            /* Dropdown - Body Level Fix */
            .user-dropdown { position: relative; z-index: 999999999; }
            .user-dropdown-menu { 
                display: none; 
                position: fixed; 
                right: 20px; 
                top: 70px; 
                background: #ffffff; 
                border: 1px solid #e5e7eb; 
                border-radius: 12px; 
                box-shadow: 0 20px 50px rgba(0,0,0,0.8); 
                min-width: 200px; 
                z-index: 999999999; 
                overflow: visible;
                animation: dropdownSlide 0.3s ease;
                transform: translateZ(0);
                will-change: transform;
            }
            @keyframes dropdownSlide {
                from {
                    opacity: 0;
                    transform: translateY(-20px) translateZ(0);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) translateZ(0);
                }
            }
            .user-dropdown-menu.show { 
                display: block !important; 
                visibility: visible !important;
                opacity: 1 !important;
                transform: translateY(0) translateZ(0) !important;
            }
            .user-dropdown-menu a, .user-dropdown-menu button { 
                display: flex; 
                align-items: center;
                width: 100%; 
                text-align: left; 
                padding: 12px 16px; 
                color: #1a1a2e; 
                text-decoration: none; 
                font-size: 14px; 
                font-weight: 600;
                border: none;
                background: none;
                cursor: pointer;
                margin: 3px;
                border-radius: 6px;
                transition: all 0.3s ease;
                position: relative;
                z-index: 1;
            }
            .user-dropdown-menu a:hover, .user-dropdown-menu button:hover { 
                background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%); 
                color: #ffffff; 
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(192,57,43,0.4);
            }
            .user-dropdown-menu a i, .user-dropdown-menu button i {
                width: 18px;
                text-align: center;
                margin-right: 10px;
                font-size: 14px;
            }

            /* Sidebar Dropdowns */
            .nav-dropdown { margin-bottom: 4px; }
            .nav-dropdown-btn { 
                display: flex; 
                align-items: center; 
                justify-content: space-between; 
                padding: 14px 24px; 
                color: #475569; 
                font-size: 14px; 
                font-weight: 500; 
                cursor: pointer; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
                background: none;
                border: none;
                width: 100%;
                text-align: left;
                position: relative;
                margin: 4px 8px;
                border-radius: 0 8px 8px 0;
                border-left: 4px solid transparent;
                box-sizing: border-box;
            }
            .nav-dropdown-btn .btn-content {
                display: flex;
                align-items: center;
                width: 22px; /* Match icon width from regular nav items */
                margin-right: 10px; /* Match icon margin from regular nav items */
            }
            .nav-dropdown-btn:hover { 
                background: rgba(192,57,43,0.08); 
                color: #c0392b; 
                transform: translateX(2px);
                border-left-color: #2980b9;
            }
            .nav-dropdown-btn::before {
                content: '';
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 4px;
                height: 0;
                background: linear-gradient(180deg, #2980b9, #c0392b);
                transition: height 0.3s ease;
            }
            .nav-dropdown-btn:hover::before {
                height: 100%;
            }
            .nav-dropdown-btn.active { 
                background: linear-gradient(90deg, rgba(192,57,43,0.15) 0%, rgba(41,128,185,0.08) 100%); 
                color: #c0392b; 
                border-left-color: #c0392b;
                font-weight: 600;
                box-shadow: 0 4px 12px rgba(192,57,43,0.15);
            }
            .nav-dropdown-btn.active::before {
                height: 100%;
                background: #c0392b;
            }
            .dropdown-arrow { 
                font-size: 10px; 
                transition: transform 0.2s;
            }
            .nav-dropdown-menu { 
                display: none; 
                background: rgba(192,57,43,0.05); 
                margin: 4px 0;
                border-radius: 8px;
                overflow: hidden;
                box-sizing: border-box;
            }
            .nav-dropdown-menu.show { display: block; }
            .nav-dropdown-item { 
                display: flex; 
                align-items: center; 
                padding: 10px 20px 10px 60px; 
                color: #475569; 
                font-size: 13px; 
                font-weight: 500;
                text-decoration: none; 
                transition: all 0.3s ease;
                border-left: 4px solid transparent;
                margin: 2px 4px;
                border-radius: 0 6px 6px 0;
                box-sizing: border-box;
            }
            .nav-dropdown-item:hover { 
                background: rgba(192,57,43,0.08); 
                color: #c0392b; 
                border-left-color: #2980b9;
                transform: translateX(2px);
            }
            .nav-dropdown-item.active { 
                background: linear-gradient(90deg, rgba(192,57,43,0.15) 0%, rgba(41,128,185,0.08) 100%); 
                color: #c0392b; 
                border-left-color: #c0392b;
                font-weight: 600;
            }

            .quick-action { 
                background: #fff; 
                border: 1px solid #e5e7eb; 
                border-radius: 6px; 
                padding: 12px 16px; 
                margin-bottom: 8px; 
                display: flex; 
                align-items: center; 
                gap: 10px; 
                cursor: pointer; 
                transition: all 0.2s ease;
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
                        <button class="user-btn" id="userDropdownBtn" type="button" onclick="toggleUserDropdown()">
                            <span class="avatar"><i class="fas fa-user"></i></span>
                            <span>{{ Auth::user()->name ?? 'User' }} - {{ Auth::user()->office->code ?? 'PICTO' }}</span>
                            <i class="fas fa-chevron-down" id="dropdownArrow" style="font-size:12px; margin-left:4px; transition:transform 0.3s ease;"></i>
                        </button>
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

        <!-- User Dropdown Menu - Body Level -->
        <div class="user-dropdown-menu" id="userDropdownMenu">
            <a href="{{ route('profile.edit') }}"><i class="fas fa-cog" style="margin-right:6px;"></i> Settings</a>
            <button type="button" onclick="document.getElementById('logoutModal').style.display='flex'; document.getElementById('userDropdownMenu').classList.remove('show');"><i class="fas fa-sign-out-alt" style="margin-right:6px;"></i> Log Out</button>
        </div>

        <!-- Notification Container -->
        <div class="notification-container" id="notificationContainer"></div>

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

            // Toggle User Dropdown
            function toggleUserDropdown() {
                const menu = document.getElementById('userDropdownMenu');
                const arrow = document.getElementById('dropdownArrow');
                
                console.log('Toggle dropdown clicked'); // Debug
                
                menu.classList.toggle('show');
                
                // Force display override
                if (menu.classList.contains('show')) {
                    menu.style.display = 'block';
                    menu.style.visibility = 'visible';
                    menu.style.opacity = '1';
                    arrow.style.transform = 'rotate(180deg)';
                    console.log('Dropdown opened'); // Debug
                } else {
                    menu.style.display = 'none';
                    menu.style.visibility = 'hidden';
                    menu.style.opacity = '0';
                    arrow.style.transform = 'rotate(0deg)';
                    console.log('Dropdown closed'); // Debug
                }
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                var dropdown = document.getElementById('userDropdown');
                var menu = document.getElementById('userDropdownMenu');
                var arrow = document.getElementById('dropdownArrow');
                
                if (dropdown && menu && !dropdown.contains(e.target)) {
                    menu.classList.remove('show');
                    menu.style.display = 'none';
                    menu.style.visibility = 'hidden';
                    menu.style.opacity = '0';
                    arrow.style.transform = 'rotate(0deg)';
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

            // Modern Notification System
            window.showNotification = function(options) {
                const {
                    type = 'info',
                    title = 'Notification',
                    message = '',
                    duration = 5000,
                    actions = null,
                    icon = null
                } = options;

                const container = document.getElementById('notificationContainer');
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;

                // Determine icon based on type
                let iconHtml = '';
                if (icon) {
                    iconHtml = `<i class="${icon}"></i>`;
                } else {
                    switch(type) {
                        case 'success':
                            iconHtml = '<i class="fas fa-check-circle"></i>';
                            break;
                        case 'warning':
                            iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
                            break;
                        case 'danger':
                            iconHtml = '<i class="fas fa-exclamation-circle"></i>';
                            break;
                        default:
                            iconHtml = '<i class="fas fa-info-circle"></i>';
                    }
                }

                let actionsHtml = '';
                if (actions && actions.length > 0) {
                    actionsHtml = '<div class="notification-actions">';
                    actions.forEach(action => {
                        actionsHtml += `<button class="notification-btn ${action.class}" onclick="${action.onclick}">${action.text}</button>`;
                    });
                    actionsHtml += '</div>';
                }

                notification.innerHTML = `
                    <div class="notification-header">
                        <div class="notification-title">${iconHtml} ${title}</div>
                        <button class="notification-close" onclick="window.removeNotification(this)">&times;</button>
                    </div>
                    <div class="notification-message">${message}</div>
                    ${actionsHtml}
                `;

                container.appendChild(notification);

                // Auto-remove after duration
                if (duration > 0) {
                    setTimeout(() => {
                        window.removeNotification(notification.querySelector('.notification-close'));
                    }, duration);
                }

                return notification;
            }

            window.removeNotification = function(element) {
                const notification = element.closest('.notification');
                if (notification) {
                    notification.classList.add('removing');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }
            }

            // Confirmation dialog function
            window.showConfirmDialog = function(options) {
                const {
                    title = 'Confirm Action',
                    message = 'Are you sure you want to proceed?',
                    confirmText = 'Confirm',
                    cancelText = 'Cancel',
                    confirmClass = 'notification-btn-confirm',
                    onConfirm = null,
                    onCancel = null
                } = options;

                return new Promise((resolve) => {
                    const notification = window.showNotification({
                        type: 'warning',
                        title: title,
                        message: message,
                        duration: 0, // Don't auto-close
                        actions: [
                            {
                                text: cancelText,
                                class: 'notification-btn-cancel',
                                onclick: `window.removeNotification(this.closest('.notification').querySelector('.notification-close')); ${onCancel ? 'window.confirmDialogCancel();' : 'window.confirmDialogCancel();'}`
                            },
                            {
                                text: confirmText,
                                class: confirmClass,
                                onclick: `window.removeNotification(this.closest('.notification').querySelector('.notification-close')); window.confirmDialogConfirm();`
                            }
                        ]
                    });

                    window.confirmDialogConfirm = () => {
                        if (onConfirm) onConfirm();
                        resolve(true);
                        delete window.confirmDialogConfirm;
                        delete window.confirmDialogCancel;
                    };

                    window.confirmDialogCancel = () => {
                        if (onCancel) onCancel();
                        resolve(false);
                        delete window.confirmDialogConfirm;
                        delete window.confirmDialogCancel;
                    };
                });
            }

            // Debug function to test notifications
            window.testNotification = function() {
                window.showNotification({
                    type: 'info',
                    title: 'Test Notification',
                    message: 'This is a test notification to verify the system is working.',
                    duration: 3000
                });
            }

            window.testConfirm = function() {
                window.showConfirmDialog({
                    title: 'Test Confirm',
                    message: 'This is a test confirmation dialog. Does it work?',
                    confirmText: 'Yes',
                    cancelText: 'No',
                    confirmClass: 'notification-btn-confirm',
                    onConfirm: function() {
                        window.showNotification({
                            type: 'success',
                            title: 'Confirmed!',
                            message: 'The confirmation dialog works correctly.',
                            duration: 3000
                        });
                    },
                    onCancel: function() {
                        window.showNotification({
                            type: 'info',
                            title: 'Cancelled',
                            message: 'The confirmation was cancelled.',
                            duration: 3000
                        });
                    }
                });
            }
        </script>
    </body>
</html>
