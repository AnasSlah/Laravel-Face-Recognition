<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المشرف | النظام الذكي</title>
    
    <style>
        :root { 
            --deep-navy: #0B1120; 
            --panel-bg: rgba(30, 41, 59, 0.7); 
            --teal-accent: #14b8a6; 
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --danger: #ef4444;
        }

        body { 
            background-color: var(--deep-navy); 
            color: var(--text-light); 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
            margin: 0; 
            display: flex; 
            min-height: 100vh;
        }

        /* القائمة الجانبية (Sidebar) */
        .sidebar {
            width: 260px;
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-left: 1px solid rgba(255,255,255,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo h2 { margin: 0; color: var(--teal-accent); font-size: 1.5em; }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(20, 184, 166, 0.1);
            color: var(--teal-accent);
        }

        /* المحتوى الرئيسي */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        /* شريط العنوان */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            padding: 15px 30px;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .header-title h1 { margin: 0; font-size: 1.5em; }
        .header-title p { margin: 5px 0 0 0; color: var(--text-muted); font-size: 0.9em; }

        .logout-btn {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .logout-btn:hover { background: var(--danger); color: white; }

        /* البطاقات الإحصائية (Glass Panels) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.05);
            padding: 25px;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .stat-card::before {
            content: ''; position: absolute; top: 0; right: 0; width: 4px; height: 100%;
            background: var(--teal-accent);
        }
        
        .stat-card.danger::before { background: var(--danger); }

        .stat-value { font-size: 2.5em; font-weight: bold; margin-bottom: 5px; }
        .stat-label { color: var(--text-muted); font-size: 1em; }

        /* جدول أحدث الحركات */
        .table-container {
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.05);
            padding: 25px;
            border-radius: 20px;
        }

        .table-container h3 { margin-top: 0; margin-bottom: 20px; color: var(--teal-accent); }

        table { width: 100%; border-collapse: collapse; text-align: right; }
        th, td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        th { color: var(--text-muted); font-weight: normal; }
        td { color: var(--text-light); }
        .status-success { color: var(--teal-accent); background: rgba(20, 184, 166, 0.1); padding: 5px 10px; border-radius: 6px; font-size: 0.85em;}
        .status-danger { color: var(--danger); background: rgba(239, 68, 68, 0.1); padding: 5px 10px; border-radius: 6px; font-size: 0.85em;}

    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <h2>OptiFace Admin</h2>
        </div>
        
        <a href="#" class="nav-link active">📊 لوحة القيادة</a>
        <a href="#" class="nav-link">📅 سجل الحضور اليومي</a>
        <a href="#" class="nav-link">👥 إدارة الطلاب</a>
        <a href="#" class="nav-link">🚨 سجل التنبيهات</a>
    </div>

    <div class="main-content">
        
        <div class="header">
            <div class="header-title">
                <h1>مرحباً، أيها المشرف 👋</h1>
                <p>إليك ملخص أداء النظام لهذا اليوم</p>
            </div>
            <a href="/logout" class="logout-btn">تسجيل خروج</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">120</div>
                <div class="stat-label">إجمالي الطلاب المسجلين</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: var(--teal-accent);">85</div>
                <div class="stat-label">حاضرون اليوم</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-value" style="color: var(--danger);">3</div>
                <div class="stat-label">محاولات تلاعب (Spoofing)</div>
            </div>
        </div>

        <div class="table-container">
            <h3>أحدث الحركات في النظام</h3>
            <table>
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الكلية</th>
                        <th>الوقت</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>أحمد محمد علي</td>
                        <td>هندسة برمجيات</td>
                        <td>08:15 AM</td>
                        <td><span class="status-success">حضور مؤكد</span></td>
                    </tr>
                    <tr>
                        <td>محمود إبراهيم</td>
                        <td>نظم معلومات</td>
                        <td>08:20 AM</td>
                        <td><span class="status-danger">حظر (محاولة تلاعب)</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>