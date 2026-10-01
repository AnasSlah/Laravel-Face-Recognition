<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- 🚀 السطر الجديد الخاص بحل مشكلة CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>لوحة التحكم | @yield('title', 'النظام الذكي')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-green: #10A04A;
            --dark-bg: #1e1e2d;
            --light-bg: #f5f8fa;
            --text-color: #3f4254;
            --sidebar-width: 260px;
        }

        body {
            margin: 0;
            font-family: 'Tajawal', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            overflow-x: hidden;
        }

        /* ======= القائمة الجانبية (Sidebar) ======= */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--dark-bg);
            min-height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease-in-out;
            z-index: 100;
        }

        .sidebar-logo {
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .nav-links {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #a2a3b7;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
        }

        .nav-links a:hover, .nav-links a.active {
            background-color: var(--primary-green);
            color: white;
            border-radius: 0 25px 25px 0; 
            margin-left: 15px;
        }

        .nav-links i { margin-left: 15px; font-size: 1.2rem; }

        /* ======= المحتوى الرئيسي (Main Content) ======= */
        .main-content {
            margin-right: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-right 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        /* الشريط العلوي */
        .topbar {
            background: white;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-color);
            cursor: pointer;
            padding: 5px;
        }

        .content-area {
            padding: 30px;
            flex-grow: 1;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 95;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* ======= التجاوب مع شاشات الموبايل ======= */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0;
                width: 100%;
            }

            .menu-toggle {
                display: block;
            }

            .topbar {
                padding: 0 15px;
            }

            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }

            .content-area {
                padding: 15px;
            }
            
            .user-greeting {
                display: none;
            }
        }

        @yield('styles')
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <i class="fa-solid fa-expand"></i> نظام الحضور
        </div>
        <ul class="nav-links">
            <li><a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> لوحة القيادة</a></li>
            <li><a href="/admin/students" class="{{ request()->is('admin/students*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> إدارة الطلاب</a></li>
            <li><a href="/admin/attendance" class="{{ request()->is('admin/attendance*') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-user"></i> سجل الحضور</a></li>
            <li><a href="/admin/alerts" class="{{ request()->is('admin/alerts*') ? 'active' : '' }}"><i class="fa-solid fa-bell"></i> التنبيهات</a></li>
            
            <!-- 🚀 السطر الجديد الخاص بإدارة المحتوى -->
            <li><a href="/admin/content" class="{{ request()->is('admin/content*') ? 'active' : '' }}"><i class="fa-solid fa-book-open"></i> إدارة المحتوى</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-right">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 style="margin:0; font-size: 1.2rem; color: #2c323f;">@yield('page_header', 'نظام الإدارة')</h2>
            </div>

            <div>
                <span class="user-greeting" style="margin-left: 20px; font-weight: bold; color: var(--text-color);">أهلاً، هيسوكا <i class="fa-solid fa-user-shield" style="color: var(--primary-green);"></i></span>
                <a href="../login" style="color: #dc3545; text-decoration: none; font-weight: bold; border-right: 1px solid #eee; padding-right: 15px;">
                    <i class="fa-solid fa-right-from-bracket"></i> خروج
                </a>
            </div>
        </header>

        <div class="content-area">
            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleMenu() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            if(menuToggle) {
                menuToggle.addEventListener('click', toggleMenu);
            }

            if(overlay) {
                overlay.addEventListener('click', toggleMenu);
            }
        });
    </script>

    @yield('scripts')
</body>
</html>