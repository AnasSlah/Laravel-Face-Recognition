<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>تسجيل الدخول | النظام الذكي</title>
    
    <style>
        :root { --primary-green: #10A04A; }
        body { background-color: #FFFFFF; font-family: 'Segoe UI', Tahoma, sans-serif; display: flex; flex-direction: column; align-items: center; padding: 20px; margin-top: 50px; }
        .logo-container img { max-height: 100px; margin-bottom: 30px; }
        
        .main-box { width: 100%; max-width: 400px; background: #FFFFFF; padding: 40px 30px; border-radius: 15px; border: 2px solid var(--primary-green); box-shadow: 0 4px 15px rgba(16, 160, 74, 0.15); text-align: center; }
        
        /* 1. الزر الأول (تسجيل دخول - تم تحويله لرابط للتوجه لصفحة الحضور) */
        .btn-login { width: 100%; background-color: var(--primary-green); color: white; border: none; padding: 15px; font-size: 1.2em; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s; margin-bottom: 15px; display: block; text-decoration: none; box-sizing: border-box; }
        .btn-login:hover { background-color: #0d823b; }
        
        /* 2. الزر الثاني (مستخدم جديد) */
        .btn-register { width: 100%; background-color: #f8f9fa; color: #333; border: 2px solid #ddd; padding: 15px; font-size: 1.1em; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s; text-decoration: none; display: block; box-sizing: border-box; margin-bottom: 25px; }
        .btn-register:hover { background-color: #e2e6ea; border-color: #ccc; }
        
        /* 3. الرابط الثالث (مشرف) */
        .admin-link { color: #888; text-decoration: none; font-weight: bold; font-size: 1em; transition: 0.3s; border-top: 1px solid #eee; padding-top: 20px; display: block; }
        .admin-link:hover { color: var(--primary-green); }
    </style>
</head>
<body>

    <div class="logo-container">
        <img src="/logo.png" alt="الشعار">
    </div>

    <div class="main-box" id="menu-box">
        <h2 style="color: var(--primary-green); margin-top: 0; margin-bottom: 30px;">مرحباً بك</h2>

        <!-- الزر الأول ينقل لصفحة الحضور -->
        <a href="/attendance" class="btn-login">تسجيل دخول</a>

        <!-- الزر الثاني -->
        <a href="/register" class="btn-register">تسجيل مستخدم جديد</a>

        <!-- الرابط الثالث -->
        <a href="/admin/login" class="admin-link">تسجيل دخول مشرف</a>
    </div>

</body>
</html>