<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول الإدارة | النظام الذكي</title>
    <style>
        :root { --primary-green: #10A04A; --dark-bg: #0f172a; }
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        
        .logo-container { margin-bottom: 25px; text-align: center; }
        .logo-container img { max-height: 90px; }

        .login-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); width: 100%; max-width: 400px; border-top: 5px solid var(--primary-green); box-sizing: border-box; }
        .login-box h2 { text-align: center; color: var(--dark-bg); margin-top: 0; margin-bottom: 30px; font-weight: 800; font-size: 1.8em; }
        
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; color: #475569; font-weight: bold; font-size: 0.95em;}
        .input-group input { width: 100%; padding: 14px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box; font-size: 1em; transition: 0.3s; outline: none; }
        .input-group input:focus { border-color: var(--primary-green); box-shadow: 0 0 0 3px rgba(16, 160, 74, 0.1); }
        
        .btn-login { width: 100%; padding: 15px; background: var(--primary-green); color: white; border: none; border-radius: 10px; font-size: 1.1em; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-login:hover { background: #0d823b; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(16, 160, 74, 0.2); }
        
        .error { color: #e11d48; background: #ffe4e6; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid #fecdd3; font-size: 0.9em; }
        
        .back-link { display: block; text-align: center; margin-top: 25px; color: #64748b; text-decoration: none; font-weight: bold; font-size: 0.95em; transition: 0.3s;}
        .back-link:hover { color: var(--primary-green); }
    </style>
</head>
<body>

    <div class="logo-container">
        <img src="/logo.png" alt="الشعار">
    </div>

    <div class="login-box">
        <h2>لوحة الإدارة</h2>
        
        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form action="/admin/login" method="POST">
            @csrf
            
            <div class="input-group">
                <label>البريد الإلكتروني (المشرف)</label>
                <input type="email" name="email" required dir="ltr" placeholder="admin@admin.com">
            </div>
            
            <div class="input-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" required dir="ltr" placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn-login">تسجيل الدخول</button>
        </form>

        <a href="/login" class="back-link">العودة للشاشة الرئيسية</a>
    </div>

</body>
</html>