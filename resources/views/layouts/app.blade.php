<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>PICTO - RMS</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/picto-rts-favicon.svg') }}">
        <link rel="shortcut icon" href="{{ asset('images/picto-rts-favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/@zxing/library@latest"></script>

        <style>
            /* Custom PICTO - RMS Design */
            
            /* Sidebar - Light Design */
            .sidebar { 
                width: 260px; 
                min-height: calc(100vh - 100px); 
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); 
                position: fixed; 
                top: 100px; 
                left: 0; 
                z-index: 1000; 
                transition: all 0.3s ease; 
                box-shadow: 4px 0 15px rgba(0,0,0,0.15);
                display: flex;
                flex-direction: column;
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
                width: 100%;
                box-sizing: border-box;
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
                --nav-accent: #64748b;
                --nav-surface-start: rgba(255,255,255,0.78);
                --nav-surface-end: rgba(248,250,252,0.92);
                --nav-hover-start: rgba(241,245,249,0.98);
                --nav-hover-end: rgba(226,232,240,0.9);
                --nav-active-start: rgba(226,232,240,0.98);
                --nav-active-end: rgba(241,245,249,0.94);
                --nav-border: rgba(148,163,184,0.26);
                display: block; 
                padding: 14px 24px; 
                color: #475569; 
                font-size: 14px;
                line-height: 1.35;
                font-weight: 500;
                letter-spacing: 0.01em;
                text-decoration: none; 
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
                border-left: 4px solid transparent;
                position: relative;
                margin: 4px 8px;
                border-radius: 16px;
                border: 1px solid rgba(226, 232, 240, 0.78);
                background: linear-gradient(135deg, var(--nav-surface-start) 0%, var(--nav-surface-end) 100%);
                overflow: hidden;
            }
            .sidebar .nav-item::before {
                content: '';
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 4px;
                height: 0;
                border-radius: 999px;
                background: linear-gradient(180deg, var(--nav-accent), var(--nav-accent));
                transition: height 0.3s ease;
            }
            .sidebar .nav-item:hover { 
                background: linear-gradient(90deg, rgba(37,99,235,0.12) 0%, rgba(248,250,252,0.96) 48%, rgba(220,38,38,0.14) 100%); 
                color: #c0392b; 
                transform: translateX(3px);
                border-left-color: #2980b9;
                border-color: rgba(148,163,184,0.28);
                box-shadow: 0 8px 18px rgba(15,23,42,0.08);
            }
            .sidebar .nav-item:hover::before {
                height: calc(100% - 12px);
            }
            .sidebar .nav-item.active { 
                background: linear-gradient(90deg, rgba(37,99,235,0.10) 0%, rgba(248,250,252,0.98) 45%, rgba(220,38,38,0.12) 100%); 
                color: #c0392b; 
                border-left-color: #c0392b;
                border-color: rgba(148,163,184,0.24);
                font-weight: 700;
                box-shadow: 0 10px 20px rgba(15,23,42,0.08);
            }
            .sidebar .nav-item.active::before {
                height: calc(100% - 10px);
                background: #c0392b;
            }
            .sidebar .nav-item i { 
                width: 24px; 
                margin-right: 12px; 
                text-align: center; 
                font-size: 15px;
                color: inherit;
            }
            .sidebar-nav-chevron {
                float: right;
                font-size: 11px !important;
                margin-top: 3px;
                margin-right: 0 !important;
                width: auto !important;
            }
            .sidebar .nav-item--dashboard {
                --nav-accent: #2563eb;
                --nav-hover-start: rgba(239, 246, 255, 0.98);
                --nav-hover-end: rgba(219, 234, 254, 0.92);
                --nav-active-start: rgba(219, 234, 254, 0.98);
                --nav-active-end: rgba(191, 219, 254, 0.94);
                --nav-border: rgba(96, 165, 250, 0.34);
            }
            .sidebar .nav-item--todo {
                --nav-accent: #0f766e;
                --nav-hover-start: rgba(240, 253, 250, 0.98);
                --nav-hover-end: rgba(204, 251, 241, 0.92);
                --nav-active-start: rgba(204, 251, 241, 0.98);
                --nav-active-end: rgba(153, 246, 228, 0.94);
                --nav-border: rgba(45, 212, 191, 0.34);
            }
            .sidebar .nav-item--documents {
                --nav-accent: #c2410c;
                --nav-hover-start: rgba(255, 247, 237, 0.98);
                --nav-hover-end: rgba(255, 237, 213, 0.92);
                --nav-active-start: rgba(255, 237, 213, 0.98);
                --nav-active-end: rgba(254, 215, 170, 0.94);
                --nav-border: rgba(251, 146, 60, 0.34);
            }
            .sidebar .nav-item--incoming {
                --nav-accent: #0284c7;
                --nav-hover-start: rgba(240, 249, 255, 0.98);
                --nav-hover-end: rgba(224, 242, 254, 0.92);
                --nav-active-start: rgba(224, 242, 254, 0.98);
                --nav-active-end: rgba(186, 230, 253, 0.94);
                --nav-border: rgba(56, 189, 248, 0.34);
            }
            .sidebar .nav-item--outgoing {
                --nav-accent: #059669;
                --nav-hover-start: rgba(236, 253, 245, 0.98);
                --nav-hover-end: rgba(209, 250, 229, 0.92);
                --nav-active-start: rgba(209, 250, 229, 0.98);
                --nav-active-end: rgba(167, 243, 208, 0.94);
                --nav-border: rgba(52, 211, 153, 0.34);
            }
            .sidebar .nav-item--financial {
                --nav-accent: #b45309;
                --nav-hover-start: rgba(255, 251, 235, 0.98);
                --nav-hover-end: rgba(254, 243, 199, 0.92);
                --nav-active-start: rgba(254, 243, 199, 0.98);
                --nav-active-end: rgba(253, 230, 138, 0.94);
                --nav-border: rgba(250, 204, 21, 0.34);
            }
            .sidebar .nav-item--financial-active {
                --nav-accent: #16a34a;
                --nav-hover-start: rgba(240, 253, 244, 0.98);
                --nav-hover-end: rgba(220, 252, 231, 0.92);
                --nav-active-start: rgba(220, 252, 231, 0.98);
                --nav-active-end: rgba(187, 247, 208, 0.94);
                --nav-border: rgba(74, 222, 128, 0.34);
            }
            .sidebar .nav-item--financial-cancelled {
                --nav-accent: #dc2626;
                --nav-hover-start: rgba(254, 242, 242, 0.98);
                --nav-hover-end: rgba(254, 226, 226, 0.92);
                --nav-active-start: rgba(254, 226, 226, 0.98);
                --nav-active-end: rgba(254, 202, 202, 0.94);
                --nav-border: rgba(248, 113, 113, 0.34);
            }
            .sidebar .nav-item--financial-finished {
                --nav-accent: #0891b2;
                --nav-hover-start: rgba(236, 254, 255, 0.98);
                --nav-hover-end: rgba(207, 250, 254, 0.92);
                --nav-active-start: rgba(207, 250, 254, 0.98);
                --nav-active-end: rgba(165, 243, 252, 0.94);
                --nav-border: rgba(34, 211, 238, 0.34);
            }
            .sidebar .nav-item--users {
                --nav-accent: #7c3aed;
                --nav-hover-start: rgba(245, 243, 255, 0.98);
                --nav-hover-end: rgba(237, 233, 254, 0.92);
                --nav-active-start: rgba(237, 233, 254, 0.98);
                --nav-active-end: rgba(221, 214, 254, 0.94);
                --nav-border: rgba(167, 139, 250, 0.34);
            }
            .sidebar .nav-item--offices {
                --nav-accent: #475569;
                --nav-hover-start: rgba(248, 250, 252, 0.98);
                --nav-hover-end: rgba(226, 232, 240, 0.92);
                --nav-active-start: rgba(226, 232, 240, 0.98);
                --nav-active-end: rgba(203, 213, 225, 0.94);
                --nav-border: rgba(148, 163, 184, 0.34);
            }
            .sidebar .profile-section { 
                padding: 20px; 
                display: flex; 
                flex-direction: column; 
                align-items: center; 
                gap: 16px; 
                border-bottom: 1px solid rgba(192,57,43,0.1);
                background: linear-gradient(135deg, rgba(192,57,43,0.05) 0%, rgba(41,128,185,0.02) 100%);
                position: relative;
                overflow: hidden;
            }
            .sidebar .profile-section::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 20px;
                right: 20px;
                height: 1px;
                background: linear-gradient(90deg, transparent, #2980b9, transparent);
                animation: slideGradient 4s ease-in-out infinite;
            }
            .sidebar .profile-avatar { 
                width: 80px; 
                height: 80px; 
                border-radius: 18px; 
                border: 3px solid #c0392b;
                box-shadow: 0 4px 12px rgba(192,57,43,0.3);
                overflow: hidden;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
            }
            .sidebar .profile-avatar-placeholder {
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                font-size: 24px;
                border-radius: 14px;
            }
            .sidebar .profile-avatar::before {
                content: '';
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: linear-gradient(45deg, #2980b9, #c0392b, #2980b9);
                border-radius: 20px;
                z-index: -1;
                animation: rotate 3s linear infinite;
            }
            @keyframes rotate {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .sidebar .profile-info { 
                flex: 1; 
            }
            .sidebar .profile-name { 
                font-size: 16px; 
                font-weight: 700; 
                color: #1a1a2e; 
                margin-bottom: 4px;
                text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            }
            .sidebar .profile-role { 
                font-size: 12px; 
                color: #64748b; 
                margin-bottom: 8px;
                font-weight: 500;
            }
            .sidebar-profile-card {
                text-align: center;
                padding: 20px;
            }
            .sidebar-profile-avatar {
                margin: 0 auto 6px auto !important;
                width: 56px !important;
                height: 56px !important;
            }
            .sidebar-profile-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 14px;
            }
            .sidebar-profile-name {
                text-transform: uppercase;
                font-size: 13px !important;
                line-height: 1.35;
                margin-bottom: 4px !important;
                letter-spacing: 0.01em;
            }
            .sidebar-profile-role {
                text-transform: uppercase;
                font-size: 11px !important;
                line-height: 1.2;
                opacity: 0.8;
                letter-spacing: 0.08em;
            }
            .sidebar .profile-rating { 
                display: flex; 
                gap: 2px; 
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
                background: rgba(255,255,255,0.56); 
                border: 1px solid rgba(226, 232, 240, 0.72);
                border-radius: 18px;
                margin: 6px 8px;
                padding: 6px 0;
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
            }
            .sidebar .nav-sub.show { display: block; }
            .sidebar .nav-sub .nav-item { 
                display: block;
                width: calc(100% - 12px);
                padding: 12px 24px 12px 60px; 
                font-size: 13px;
                line-height: 1.3;
                margin: 4px 6px;
                border-radius: 14px;
                box-sizing: border-box;
            }
            .sidebar .nav-sub .nav-item--compact {
                white-space: nowrap;
                padding: 10px 18px 10px 44px;
                font-size: 13px;
                letter-spacing: 0;
            }
            .sidebar .nav-sub .nav-item--compact i {
                width: 18px;
                margin-right: 8px;
                font-size: 13px;
            }
            .sidebar .nav-sub .nav-item--nested {
                margin-left: 22px;
                width: calc(100% - 34px);
                padding-left: 52px;
                font-size: 12.5px;
                color: #64748b;
                background: linear-gradient(135deg, rgba(255,255,255,0.7) 0%, rgba(248,250,252,0.88) 100%);
            }
            .sidebar .nav-sub .nav-item--nested:hover {
                background: linear-gradient(90deg, rgba(37,99,235,0.12) 0%, rgba(248,250,252,0.96) 46%, rgba(220,38,38,0.16) 100%);
                color: #c0392b;
                box-shadow: 0 8px 18px rgba(15,23,42,0.08);
            }
            .sidebar .nav-sub .nav-item--nested.active {
                background: linear-gradient(90deg, rgba(37,99,235,0.14) 0%, rgba(248,250,252,0.98) 42%, rgba(220,38,38,0.18) 100%);
                color: #c0392b;
                box-shadow: 0 10px 20px rgba(15,23,42,0.08);
            }
            .sidebar .nav-sub .nav-item--nested::after {
                content: '';
                position: absolute;
                left: 24px;
                top: 50%;
                width: 12px;
                height: 1px;
                background: rgba(100,116,139,0.45);
                transform: translateY(-50%);
            }
            .sidebar .nav-sub .nav-item--nested:hover::after,
            .sidebar .nav-sub .nav-item--nested.active::after {
                background: rgba(192,57,43,0.6);
            }
            .sidebar .nav-sub .nav-item--nested .nav-item-nested-arrow {
                font-size: 11px !important;
                width: 14px !important;
                margin-right: 6px !important;
                transform: rotate(90deg);
                opacity: 0.75;
            }
            .sidebar .nav-sub .nav-item--nested:hover .nav-item-nested-arrow,
            .sidebar .nav-sub .nav-item--nested.active .nav-item-nested-arrow {
                opacity: 1;
                color: #c0392b;
            }
            .sidebar .sidebar-admin-section {
                text-align: center;
                padding: 10px 0;
                margin: auto 0 0;
                position: static;
                z-index: 2;
                background: linear-gradient(180deg, rgba(248,250,252,0) 0%, rgba(248,250,252,0.94) 18%, rgba(255,255,255,0.98) 100%);
                border-top: 1px solid rgba(192,57,43,0.08);
            }
            .sidebar .sidebar-admin-links {
                display: flex;
                flex-direction: column;
                gap: 6px;
                padding: 0 8px 14px;
            }
            .sidebar .sidebar-admin-link {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: auto;
                min-height: 44px;
                padding: 12px 20px !important;
                white-space: nowrap;
                font-size: 13px !important;
                line-height: 1.25;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }
            .sidebar-admin-link i {
                font-size: 14px !important;
                margin-right: 10px !important;
            }
            .sidebar .nav-sub .nav-item:hover,
            .sidebar .nav-sub .nav-item.active {
                transform: translateX(2px);
            }

            /* Static Header - Modern Design */
            .static-header { 
                position: fixed; 
                top: 0; 
                left: 0; 
                right: 0; 
                height: 100px; 
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); 
                border-bottom: 2px solid rgba(192,57,43,0.1); 
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                backdrop-filter: blur(10px);
                z-index: 1000;
                display: flex;
                align-items: center;
            }
            .header-content { 
                max-width: 1200px; 
                margin: 0 auto; 
                padding: 0 32px; 
                height: 100%; 
                display: flex; 
                align-items: center; 
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 20px;
            }
            .header-logo { 
                display: flex; 
                align-items: center; 
                gap: 18px;
            }
            .header-title {
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 4px;
                text-align: left;
            }
            .header-title h2 { 
                margin: 0; 
                color: #1a1a2e; 
                font-size: 22px; 
                line-height: 1.18;
                font-weight: 700; 
                text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            }
            .header-title p { 
                margin: 0; 
                color: #64748b; 
                font-size: 13px; 
                line-height: 1.35;
                letter-spacing: 0.01em;
            }
            .header-account { 
                display: flex; 
                align-items: center; 
                flex-shrink: 0;
            }
            .header-account .user-btn {
                background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%); 
                color: #ffffff; 
                padding: 6px 12px; 
                border-radius: 20px; 
                font-size: 11px; 
                font-weight: 700; 
                display: flex; 
                align-items: center; 
                gap: 6px; 
                cursor: pointer; 
                border: 2px solid rgba(255,255,255,0.2); 
                box-shadow: 0 3px 8px rgba(192,57,43,0.4); 
                transition: all 0.3s ease; 
                min-height: 36px; 
                width: auto; 
                justify-content: space-between;
                white-space: nowrap;
            }

            /* Main Content - Modern Design */
            .main-content { 
                margin-left: 260px; 
                min-height: 100vh; 
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%); 
                transition: all 0.3s; 
                display: flex; 
                flex-direction: column;
                position: relative;
                padding-top: 220px;
                overflow-x: hidden;
            }
            .main-content.expanded { 
                margin-left: 0; 
            }
            .main-content::before {
                content: '';
                position: fixed;
                top: 0;
                left: 260px;
                right: 0;
                bottom: 0;
                background: 
                    radial-gradient(circle at 20% 80%, rgba(192,57,43,0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(41,128,185,0.1) 0%, transparent 50%),
                    radial-gradient(circle at 40% 40%, rgba(245,158,11,0.05) 0%, transparent 50%);
                pointer-events: none;
                z-index: -1;
                transition: left 0.3s ease;
            }
            .main-content.expanded::before {
                left: 0;
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
                position: fixed;
                top: 100px;
                left: 260px;
                right: 0;
                z-index: 997;
                transition: left 0.3s ease;
            }
            .main-content.expanded .top-bar {
                left: 0;
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
            .top-bar .user-area .notification-dropdown {
                position: relative;
            }
            .top-bar .user-area .notification-btn {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                border: 1px solid rgba(148,163,184,0.28);
                background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(241,245,249,0.95) 100%);
                color: #334155;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                position: relative;
                box-shadow: 0 10px 22px rgba(15,23,42,0.08);
                transition: transform 0.22s ease, box-shadow 0.22s ease, color 0.22s ease;
            }
            .top-bar .user-area .notification-btn:hover,
            .top-bar .user-area .notification-btn.active {
                transform: translateY(-2px);
                color: #c0392b;
                box-shadow: 0 14px 28px rgba(15,23,42,0.12);
            }
            .top-bar .user-area .notification-btn i {
                font-size: 16px;
            }
            .notification-badge {
                position: absolute;
                top: -6px;
                right: -5px;
                min-width: 20px;
                height: 20px;
                padding: 0 6px;
                border-radius: 999px;
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
                color: #ffffff;
                font-size: 11px;
                font-weight: 700;
                display: none;
                align-items: center;
                justify-content: center;
                border: 2px solid #ffffff;
                box-shadow: 0 8px 16px rgba(153,27,27,0.24);
            }
            .notification-badge.show {
                display: inline-flex;
            }
            .top-bar .user-area .user-btn { 
                background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%); 
                color: #ffffff; 
                padding: 6px 20px; 
                border-radius: 20px; 
                font-size: 14px; 
                font-weight: 700; 
                text-transform: uppercase; 
                display: flex; 
                align-items: center; 
                gap: 6px; 
                cursor: pointer; 
                border: 2px solid rgba(255,255,255,0.2);
                box-shadow: 0 3px 8px rgba(192,57,43,0.4);
                transition: all 0.3s ease;
                min-height: 36px;
                width: 100%;
                justify-content: center;
                min-width: 250px;
            }
            .top-bar .user-area .user-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(192,57,43,0.4);
            }
            .top-bar .user-area .user-btn .avatar { 
                width: 24px; 
                height: 24px; 
                border-radius: 50%; 
                background: linear-gradient(135deg, #2980b9 0%, #64b5f6 100%); 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                font-size: 12px;
                border: 2px solid rgba(255,255,255,0.3);
            }

            .page-header { 
                padding: 12px 32px 8px; 
                background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(192,57,43,0.1);
                position: fixed;
                top: 164px;
                left: 260px;
                right: 0;
                z-index: 998;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                transition: left 0.3s ease;
            }
            .main-content.expanded .page-header {
                left: 0;
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
                padding: 36px 32px 24px; 
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
                border-radius: 20px; 
                padding: 28px 32px; 
                color: #fff; 
                display: flex; 
                align-items: center; 
                justify-content: space-between; 
                min-height: 110px;
                position: relative;
                overflow: hidden;
                backdrop-filter: blur(20px);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(255,255,255,0.2);
                box-shadow: 
                    0 8px 32px rgba(0,0,0,0.1),
                    inset 0 1px 0 rgba(255,255,255,0.2);
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
                margin: 20px 0 24px 0;
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
                font-size: 14px; 
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
                background: linear-gradient(90deg, rgba(37,99,235,0.10) 0%, rgba(248,250,252,0.98) 48%, rgba(220,38,38,0.11) 100%); 
                transform: translateY(-1px);
                box-shadow: 0 8px 18px rgba(15,23,42,0.06);
                position: relative;
            }
            .table-card table tbody tr:hover td { 
                color: #1a1a2e; 
                font-weight: 600;
            }

            /* Buttons - Modern Design */
            .btn-red,
            .btn-blue,
            .btn-orange,
            .btn-danger,
            .btn-green,
            .btn-gray {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                line-height: 1;
                vertical-align: middle;
                white-space: nowrap;
            }

            .btn-red i,
            .btn-blue i,
            .btn-orange i,
            .btn-danger i,
            .btn-green i,
            .btn-gray i {
                flex-shrink: 0;
            }

            .btn-red { 
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); 
                color: #fff; 
                padding: 6px 12px; 
                border-radius: 8px; 
                font-size: 14px; 
                font-weight: 600; 
                border: none; 
                cursor: pointer; 
                transition: all 0.3s ease;
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
                font-size: 14px; 
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
                padding: 5px 20px; 
                border-radius: 8px; 
                font-size: 14px; 
                font-weight: 600; 
                border: none; 
                cursor: pointer; 
                text-decoration: none; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(234,88,12,0.3);
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .btn-orange:hover { 
                background: linear-gradient(135deg, #c2410c 0%, #b91c1c 100%); 
                color: #fff; 
                transform: translateY(-2px); 
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
                font-size: 14px; 
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
                padding: 5px 20px; 
                border-radius: 8px; 
                font-size: 14px; 
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

            .detail-header-actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .detail-header-actions .btn-orange,
            .detail-header-actions .btn-gray {
                min-width: 98px;
                min-height: 36px;
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

            /* Global shared notification style (todo-style) */
            .notification-container {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                z-index: 99999 !important;
                pointer-events: none !important;
                width: auto !important;
                max-width: 560px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0 12px !important;
            }
            .notification-container.has-backdrop::before {
                content: none;
            }
            .notification {
                background: #fff !important;
                border: 1px solid rgba(148, 163, 184, 0.35) !important;
                border-left: none !important;
                border-radius: 10px !important;
                padding: 16px 18px !important;
                margin-bottom: 12px !important;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12) !important;
                min-width: 320px !important;
                max-width: 460px !important;
                pointer-events: all !important;
                animation: toastAppear 0.26s ease-out !important;
                position: relative !important;
                overflow: hidden !important;
            }
            .active-filter-list {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
            }
            .active-filter-label {
                color: #666;
                font-size: 15px;
            }
            .active-filter-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 12px;
                border-radius: 999px;
                background: linear-gradient(135deg, rgba(226, 232, 240, 0.92) 0%, rgba(248, 250, 252, 0.98) 100%);
                border: 1px solid rgba(226, 232, 240, 0.95);
                box-shadow: 0 3px 10px rgba(148, 163, 184, 0.12);
                color: #64748b;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                white-space: nowrap;
            }
            .active-filter-remove {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 16px;
                height: 16px;
                border-radius: 999px;
                background: rgba(248, 113, 113, 0.12);
                color: #ef9aa9;
                text-decoration: none;
                font-size: 11px;
                font-weight: 700;
                line-height: 1;
                transition: all 0.2s ease;
            }
            .active-filter-remove:hover {
                background: rgba(248, 113, 113, 0.2);
                color: #e47a8d;
            }
            .active-filter-pill a {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 16px !important;
                height: 16px !important;
                border-radius: 999px !important;
                background: rgba(248, 113, 113, 0.12) !important;
                color: #ef9aa9 !important;
                text-decoration: none !important;
                font-size: 11px !important;
                font-weight: 700 !important;
                line-height: 1 !important;
                margin-left: 0 !important;
                padding: 0 !important;
                transition: all 0.2s ease !important;
            }
            .active-filter-pill a:hover {
                background: rgba(248, 113, 113, 0.2) !important;
                color: #e47a8d !important;
            }
            .notification.success { border-color: #16a34a !important; }
            .notification.warning { border-color: #f59e0b !important; }
            .notification.info { border-color: #2563eb !important; }
            .notification.danger, .notification.error { border-color: #dc2626 !important; }

            .notification-header { display: flex !important; align-items: center !important; justify-content: space-between !important; margin-bottom: 8px !important; }
            .notification-title { font-weight: 700 !important; font-size: 14px !important; color: #111827 !important; display: flex !important; align-items: center !important; gap: 8px !important; }
            .notification-close { background: transparent !important; border: none !important; color: #6b7280 !important; font-size: 18px !important; cursor: pointer !important; width: 24px !important; height: 24px !important; display: flex !important; align-items: center !important; justify-content: center !important; border-radius: 6px !important; transition: all 0.2s ease !important; }
            .notification-close:hover { background: rgba(156, 163, 175, 0.2) !important; color: #111827 !important; }
            .notification-message { font-size: 14px !important; line-height: 1.4 !important; color: #4b5563 !important; }
            .notification.removing { animation: toastDismiss 0.2s ease-out forwards !important; }

            @keyframes toastAppear { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes toastDismiss { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(-10px); } }

            /* Upload Option Buttons - Modern Design */
            .upload-option-btn { 
                color: #fff; 
                padding: 20px 16px; 
                border-radius: 12px; 
                border: none; 
                cursor: pointer; 
                text-decoration: none; 
                display: flex; 
                flex-direction: column; 
                align-items: center; 
                justify-content: center; 
                gap: 4px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                position: relative;
                overflow: hidden;
                min-height: 90px;
            }
            .upload-option-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 70%);
                pointer-events: none;
            }
            .upload-option-btn:hover { 
                transform: translateY(-2px); 
                box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            }
            .upload-option-btn:active {
                transform: translateY(0);
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
            .search-input { border: 1px solid rgba(148, 163, 184, 0.32); border-radius: 8px; padding: 10px 14px; font-size: 13px; outline: none; transition: all 0.2s ease; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); }
            .search-input:focus { border-color: #c0392b; box-shadow: 0 0 0 3px rgba(192,57,43,0.1); }
            .search-input:hover { border-color: rgba(96, 165, 250, 0.5); box-shadow: 0 6px 14px rgba(15,23,42,0.06); transform: translateY(-1px); background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }

            /* Form controls - Standardized */
            .form-group { margin-bottom: 16px; }
            .form-group label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
            .form-control { width: 100%; border: 1px solid rgba(148, 163, 184, 0.28); border-radius: 8px; padding: 12px 14px; font-size: 14px; outline: none; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease, background 0.25s ease; }
            .form-control:focus { border-color: #c0392b; box-shadow: 0 0 0 3px rgba(192,57,43,0.1), 0 8px 18px rgba(15,23,42,0.06); transform: translateY(-1px); }
            .form-control:hover { border-color: rgba(96, 165, 250, 0.46); box-shadow: 0 6px 16px rgba(15,23,42,0.05); transform: translateY(-1px); background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }
            select.form-control { appearance: auto; }
            textarea.form-control { resize: vertical; min-height: 80px; }
            textarea.form-control:hover { border-color: rgba(96, 165, 250, 0.46); box-shadow: 0 6px 16px rgba(15,23,42,0.05); transform: translateY(-1px); background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }
            input[type="file"].form-control { cursor: pointer; }
            input[type="file"].form-control:hover { border-color: rgba(96, 165, 250, 0.46); box-shadow: 0 6px 16px rgba(15,23,42,0.05); transform: translateY(-1px); background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }
            input[type="date"].form-control:hover { border-color: rgba(96, 165, 250, 0.46); box-shadow: 0 6px 16px rgba(15,23,42,0.05); transform: translateY(-1px); background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }
            input[type="number"].form-control:hover { border-color: rgba(96, 165, 250, 0.46); box-shadow: 0 6px 16px rgba(15,23,42,0.05); transform: translateY(-1px); background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }
            input[type="email"].form-control:hover { border-color: rgba(96, 165, 250, 0.46); box-shadow: 0 6px 16px rgba(15,23,42,0.05); transform: translateY(-1px); background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }
            input[type="password"].form-control:hover { border-color: rgba(96, 165, 250, 0.46); box-shadow: 0 6px 16px rgba(15,23,42,0.05); transform: translateY(-1px); background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }

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
                right: 35px; 
                top: 160px; 
                background: #ffffff; 
                border: 1px solid #e5e7eb; 
                border-radius: 10px; 
                box-shadow: 0 15px 35px rgba(0,0,0,0.8); 
                min-width: 180px; 
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
                padding: 8px 12px; 
                color: #1a1a2e; 
                font-size: 12px; 
                font-weight: 600;
                border: none;
                background: none;
                cursor: pointer;
                margin: 2px;
                border-radius: 5px;
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
                width: 16px;
                text-align: center;
                margin-right: 8px;
                font-size: 12px;
            }
            .notification-dropdown-menu {
                display: none;
                position: fixed;
                right: 305px;
                top: 160px;
                width: 360px;
                max-width: calc(100vw - 32px);
                background: linear-gradient(180deg, rgba(255,255,255,0.99) 0%, rgba(248,250,252,0.98) 100%);
                border: 1px solid rgba(226,232,240,0.95);
                border-radius: 18px;
                box-shadow: 0 24px 50px rgba(15,23,42,0.18);
                overflow: hidden;
                z-index: 999999998;
            }
            .notification-dropdown-menu.show {
                display: block;
            }
            .notification-panel-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 16px 18px 12px;
                border-bottom: 1px solid rgba(226,232,240,0.9);
                background: linear-gradient(90deg, rgba(37,99,235,0.06) 0%, rgba(255,255,255,0.98) 48%, rgba(220,38,38,0.08) 100%);
            }
            .notification-panel-title {
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
            }
            .notification-panel-subtitle {
                margin-top: 2px;
                font-size: 12px;
                color: #64748b;
            }
            .notification-panel-action {
                border: none;
                background: none;
                color: #2563eb;
                font-size: 12px;
                font-weight: 700;
                cursor: pointer;
                padding: 0;
            }
            .notification-panel-action:hover {
                color: #1d4ed8;
            }
            .notification-panel-body {
                max-height: 420px;
                overflow-y: auto;
                padding: 8px;
            }
            .notification-feed-empty {
                padding: 22px 18px;
                text-align: center;
                color: #64748b;
                font-size: 13px;
            }
            .notification-feed-item {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding: 12px 12px;
                margin: 6px 0;
                border-radius: 16px;
                text-decoration: none;
                color: inherit;
                border: 1px solid rgba(226,232,240,0.86);
                background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.92) 100%);
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            }
            .notification-feed-item:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 24px rgba(15,23,42,0.08);
                border-color: rgba(148,163,184,0.38);
            }
            .notification-feed-item.is-unread {
                background: linear-gradient(90deg, rgba(37,99,235,0.08) 0%, rgba(255,255,255,0.98) 48%, rgba(220,38,38,0.08) 100%);
                border-color: rgba(147,197,253,0.5);
            }
            .notification-feed-icon {
                width: 38px;
                height: 38px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: linear-gradient(135deg, rgba(37,99,235,0.12) 0%, rgba(220,38,38,0.12) 100%);
                color: #1e3a8a;
            }
            .notification-feed-item[data-type="success"] .notification-feed-icon {
                background: linear-gradient(135deg, rgba(34,197,94,0.16) 0%, rgba(21,128,61,0.12) 100%);
                color: #166534;
            }
            .notification-feed-item[data-type="danger"] .notification-feed-icon {
                background: linear-gradient(135deg, rgba(248,113,113,0.18) 0%, rgba(153,27,27,0.12) 100%);
                color: #991b1b;
            }
            .notification-feed-item[data-type="warning"] .notification-feed-icon {
                background: linear-gradient(135deg, rgba(251,191,36,0.18) 0%, rgba(217,119,6,0.14) 100%);
                color: #92400e;
            }
            .notification-feed-content {
                min-width: 0;
                flex: 1;
            }
            .notification-feed-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 5px;
            }
            .notification-feed-title {
                font-size: 13px;
                font-weight: 700;
                color: #0f172a;
                line-height: 1.35;
            }
            .notification-feed-time {
                font-size: 11px;
                color: #64748b;
                white-space: nowrap;
            }
            .notification-feed-message {
                font-size: 12.5px;
                color: #475569;
                line-height: 1.5;
            }
            .notification-feed-pill {
                display: inline-flex;
                align-items: center;
                margin-top: 7px;
                padding: 4px 8px;
                border-radius: 999px;
                background: rgba(148,163,184,0.14);
                color: #475569;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.04em;
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
                border-left: 4px solid #c0392b;
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
            .table-header-filter-link {
                display: block;
                padding: 9px 12px;
                text-decoration: none;
                color: #334155;
                font-size: 13px;
                transition: background-color 0.18s ease, color 0.18s ease;
            }
            .table-header-filter-link:hover {
                background: linear-gradient(90deg, rgba(37,99,235,0.09) 0%, rgba(255,255,255,0.96) 45%, rgba(220,38,38,0.11) 100%);
                color: #0f172a;
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
        <!-- Static Header -->
        <div class="static-header">
            <div class="header-content">
                <div class="header-logo">
                    <div style="width:80px;height:80px;border-radius:28px;display:flex;align-items:center;justify-content:center;overflow:hidden;box-shadow:0 10px 24px rgba(15,23,42,0.12);border:1px solid rgba(22,59,140,0.08);background:linear-gradient(145deg,#ffffff 0%,#f7faff 100%);">
                        <img src="{{ asset('images/picto-rts-mark.svg') }}" alt="PICTO RMS Logo" style="width:100%;height:100%;object-fit:contain;padding:0;">
                    </div>
                    <div class="header-title">
                        <h2>PICTO - Records Monitoring System</h2>
                        <p>Provincial Information and Communications Technology Office</p>
                    </div>
                </div>
            </div>
        </div>

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
                    <div class="notification-dropdown" id="notificationDropdown">
                        <button class="notification-btn" id="notificationDropdownBtn" type="button" onclick="toggleNotificationDropdown()">
                            <i class="fa-solid fa-bell"></i>
                            <span class="notification-badge" id="notificationBadge">0</span>
                        </button>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <button class="user-btn" id="userDropdownBtn" type="button" onclick="toggleUserDropdown()">
                            <span style="flex: 1; text-align: center;">{{ Auth::user()->name ?? 'User' }} - {{ Auth::user()->office->code ?? 'PICTO' }}</span>
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
                &copy; Copyright {{ date('Y') }}. All rights reserved. <a href="#">PICTO - Records Monitoring System</a>.
            </div>
        </div>

        <!-- User Dropdown Menu - Body Level -->
        <div class="user-dropdown-menu" id="userDropdownMenu">
            <a href="{{ route('profile.edit') }}"><i class="fas fa-cog" style="margin-right:6px;"></i> Settings</a>
            <button type="button" onclick="document.getElementById('logoutModal').style.display='flex'; document.getElementById('userDropdownMenu').classList.remove('show');"><i class="fas fa-sign-out-alt" style="margin-right:6px;"></i> Log Out</button>
        </div>

        <div class="notification-dropdown-menu" id="notificationDropdownMenu">
            <div class="notification-panel-header">
                <div>
                    <div class="notification-panel-title">Notifications</div>
                    <div class="notification-panel-subtitle">Updates, forwards, and reminders</div>
                </div>
                <button type="button" class="notification-panel-action" onclick="markAllNotificationsRead()">Mark all read</button>
            </div>
            <div class="notification-panel-body" id="notificationFeed">
                <div class="notification-feed-empty">Loading notifications...</div>
            </div>
            <div style="padding: 10px 14px 14px; border-top: 1px solid rgba(226,232,240,0.9); background: rgba(248,250,252,0.92);">
                <a href="{{ route('notifications.index') }}" style="display:flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; color:#2563eb; font-size:13px; font-weight:700;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> View All Notifications
                </a>
            </div>
        </div>

        <!-- Notification Container -->
        <div class="notification-container" id="notificationContainer"></div>

        <!-- Logout Confirmation Modal -->
        <div id="logoutModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.28); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); z-index:9999; align-items:center; justify-content:center;">
            <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 2px dashed rgba(192,57,43,0.2); border-radius: 16px; padding: 40px; max-width:400px; width:90%; text-align:center; box-shadow:0 10px 40px rgba(0,0,0,0.2); animation: modalIn 0.25s ease;">
                <div style="width:70px; height:70px; border-radius:50%; border:3px solid #8b0000; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                    <i class="fas fa-sign-out-alt" style="font-size:32px; color:#8b0000;"></i>
                </div>
                <h3 style="color: #1a1a2e; margin-bottom: 8px; font-weight:600;">Confirm Logout</h3>
                <p style="color: #64748b; margin-bottom: 20px;">Are you sure you want to logout?</p>
                <div style="margin-top:24px; display:flex; justify-content:center; gap:12px;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-red">
                            <i class="fas fa-sign-out-alt"></i> Yes, Logout
                        </button>
                    </form>
                    <button type="button" onclick="document.getElementById('logoutModal').style.display='none'" class="btn-gray">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
        <script>
            const notificationState = {
                hasLoadedOnce: false,
                seenIds: new Set(),
            };

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
                const notificationMenu = document.getElementById('notificationDropdownMenu');
                const notificationBtn = document.getElementById('notificationDropdownBtn');
                
                menu.classList.toggle('show');
                notificationMenu.classList.remove('show');
                notificationBtn.classList.remove('active');
                
                if (menu.classList.contains('show')) {
                    menu.style.display = 'block';
                    menu.style.visibility = 'visible';
                    menu.style.opacity = '1';
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    menu.style.display = 'none';
                    menu.style.visibility = 'hidden';
                    menu.style.opacity = '0';
                    arrow.style.transform = 'rotate(0deg)';
                }
            }

            function toggleNotificationDropdown() {
                const menu = document.getElementById('notificationDropdownMenu');
                const btn = document.getElementById('notificationDropdownBtn');
                const userMenu = document.getElementById('userDropdownMenu');
                const userArrow = document.getElementById('dropdownArrow');

                const isShowing = menu.classList.toggle('show');
                btn.classList.toggle('active', isShowing);

                userMenu.classList.remove('show');
                userMenu.style.display = 'none';
                userMenu.style.visibility = 'hidden';
                userMenu.style.opacity = '0';
                userArrow.style.transform = 'rotate(0deg)';

                if (isShowing) {
                    loadNotificationFeed();
                }
            }

            async function loadNotificationFeed() {
                const feed = document.getElementById('notificationFeed');

                try {
                    const response = await fetch('{{ route('notifications.feed') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load notifications.');
                    }

                    const data = await response.json();
                    processIncomingNotifications(data.items || []);
                    renderNotificationFeed(data.items || []);
                    updateNotificationBadge(data.unread_count || 0);
                } catch (error) {
                    feed.innerHTML = '<div class="notification-feed-empty">Unable to load notifications right now.</div>';
                }
            }

            function processIncomingNotifications(items) {
                const incomingIds = new Set();
                const freshItems = [];

                items.forEach((item) => {
                    if (!item || !item.id) {
                        return;
                    }

                    incomingIds.add(item.id);

                    if (notificationState.hasLoadedOnce && !notificationState.seenIds.has(item.id) && !item.read_at) {
                        freshItems.push(item);
                    }
                });

                notificationState.seenIds = incomingIds;

                if (notificationState.hasLoadedOnce && freshItems.length) {
                    const latestItem = freshItems[0];
                    window.showNotification({
                        type: latestItem.type || 'info',
                        title: latestItem.title || 'New notification',
                        message: latestItem.message || '',
                        icon: latestItem.icon || 'fa-solid fa-bell',
                        duration: 4500
                    });
                }

                notificationState.hasLoadedOnce = true;
            }

            function renderNotificationFeed(items) {
                const feed = document.getElementById('notificationFeed');

                if (!items.length) {
                    feed.innerHTML = '<div class="notification-feed-empty">No new notifications yet.</div>';
                    return;
                }

                feed.innerHTML = items.map((item) => {
                    const unreadClass = item.read_at ? '' : ' is-unread';
                    const url = item.url || '#';
                    const pill = item.category ? `<span class="notification-feed-pill">${escapeHtml(item.category)}</span>` : '';
                    const syntheticAttr = item.synthetic ? 'true' : 'false';

                    return `
                        <a href="${url}" class="notification-feed-item${unreadClass}" data-id="${escapeHtml(item.id)}" data-synthetic="${syntheticAttr}" data-type="${escapeHtml(item.type || 'info')}" onclick="handleNotificationItemClick(event, this)">
                            <span class="notification-feed-icon"><i class="${escapeHtml(item.icon || 'fa-solid fa-bell')}"></i></span>
                            <span class="notification-feed-content">
                                <span class="notification-feed-meta">
                                    <span class="notification-feed-title">${escapeHtml(item.title || 'Notification')}</span>
                                    <span class="notification-feed-time">${escapeHtml(item.time_label || '')}</span>
                                </span>
                                <span class="notification-feed-message">${escapeHtml(item.message || '')}</span>
                                ${pill}
                            </span>
                        </a>
                    `;
                }).join('');
            }

            async function handleNotificationItemClick(event, element) {
                const id = element.dataset.id;
                const synthetic = element.dataset.synthetic === 'true';

                if (!synthetic && id) {
                    try {
                        await fetch(`{{ url('/notifications') }}/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                    } catch (error) {
                    }
                }

                document.getElementById('notificationDropdownMenu').classList.remove('show');
                document.getElementById('notificationDropdownBtn').classList.remove('active');
            }

            async function markAllNotificationsRead() {
                try {
                    await fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    loadNotificationFeed();
                } catch (error) {
                    window.showNotification({
                        type: 'danger',
                        title: 'Notification Error',
                        message: 'Unable to mark notifications as read right now.',
                        duration: 3000
                    });
                }
            }

            function updateNotificationBadge(count) {
                const badge = document.getElementById('notificationBadge');
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.toggle('show', count > 0);
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                var dropdown = document.getElementById('userDropdown');
                var menu = document.getElementById('userDropdownMenu');
                var arrow = document.getElementById('dropdownArrow');
                var notificationDropdown = document.getElementById('notificationDropdown');
                var notificationMenu = document.getElementById('notificationDropdownMenu');
                var notificationBtn = document.getElementById('notificationDropdownBtn');
                
                if (dropdown && menu && !dropdown.contains(e.target)) {
                    menu.classList.remove('show');
                    menu.style.display = 'none';
                    menu.style.visibility = 'hidden';
                    menu.style.opacity = '0';
                    arrow.style.transform = 'rotate(0deg)';
                }

                if (notificationDropdown && notificationMenu && !notificationDropdown.contains(e.target) && !notificationMenu.contains(e.target)) {
                    notificationMenu.classList.remove('show');
                    notificationBtn.classList.remove('active');
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
                    container.classList.add('has-backdrop');
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
                    const container = notification.parentNode;
                    notification.classList.add('removing');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                        if (container && !container.querySelector('.notification')) {
                            container.classList.remove('has-backdrop');
                        }
                    }, 300);
                }
            }

            // Confirmation dialog using modern notification style (buttons in toast-like card)
            window.showConfirmDialog = function(options) {
                const {
                    title = 'Confirm Action',
                    message = 'Are you sure you want to proceed?',
                    confirmText = 'Yes',
                    cancelText = 'No',
                    confirmClass = 'notification-btn-confirm',
                    onConfirm = null,
                    onCancel = null
                } = options;

                return new Promise((resolve) => {
                    const notification = window.showNotification({
                        type: 'warning',
                        title: title,
                        message: message,
                        duration: 0,
                        actions: [
                            {
                                text: cancelText,
                                class: 'notification-btn-cancel',
                                onclick: `const n=this.closest('.notification'); window.removeNotification(n.querySelector('.notification-close')); if(typeof window.confirmDialogCancel==='function') window.confirmDialogCancel();`
                            },
                            {
                                text: confirmText,
                                class: confirmClass,
                                onclick: `const n=this.closest('.notification'); window.removeNotification(n.querySelector('.notification-close')); if(typeof window.confirmDialogConfirm==='function') window.confirmDialogConfirm();`
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

            function hasLegacyAlert() {
                return document.querySelector('.alert-success, .alert-danger, .alert-warning, .alert-info, .alert-error') !== null;
            }

            document.addEventListener('DOMContentLoaded', function() {
                loadNotificationFeed();
                setInterval(loadNotificationFeed, 60000);
            });
        </script>
    </body>
</html>
