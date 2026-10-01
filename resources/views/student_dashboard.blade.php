<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الطالب | جامعة أم درمان الأهلية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-green: #009A44; 
            --light-green: #E6F5EC;
            --dark-text: #2c323f;
            --gray-text: #6c757d;
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
        }

        body { font-family: 'Tajawal', sans-serif; background-color: var(--bg-color); color: var(--dark-text); margin: 0; padding: 0; }
        
        /* الهيدر */
        .header { background-color: #ffffff; color: var(--dark-text); padding: 10px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-bottom: 3px solid var(--primary-green); }
        .header-info { display: flex; align-items: center; gap: 15px; }
        .header-info img { max-height: 55px; }
        .header-text h1 { margin: 0; font-size: 1.5rem; color: var(--primary-green); }
        
        .header-actions { display: flex; align-items: center; gap: 15px; }
        .header-actions .logout-btn { background: var(--light-green); color: var(--primary-green); text-decoration: none; padding: 8px 15px; border-radius: 5px; transition: 0.3s; font-weight: bold; border: 1px solid var(--primary-green); }
        .header-actions .logout-btn:hover { background: var(--primary-green); color: white; }
        
        /* التقسيمة الرئيسية */
        .container { padding: 30px; max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 2.5fr; gap: 25px; }
        @media (max-width: 768px) { .container { grid-template-columns: 1fr; } .header { flex-direction: column; gap: 15px; text-align: center; } }
        
        /* الكروت العامة */
        .card { background: var(--card-bg); border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border-top: 4px solid var(--primary-green); transition: transform 0.3s; margin-bottom: 25px; }
        .card:hover { transform: translateY(-3px); }
        .card-title { font-size: 1.2rem; color: var(--primary-green); border-bottom: 2px solid var(--light-green); padding-bottom: 10px; margin-top: 0; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        /* كارت البروفايل */
        .profile-card { text-align: center; }
        .profile-pic { width: 100px; height: 100px; border-radius: 50%; border: 3px solid var(--primary-green); padding: 3px; margin-bottom: 15px; object-fit: cover; }
        .profile-name { font-size: 1.3rem; font-weight: bold; margin: 0; color: var(--primary-green); }
        .profile-dept { color: var(--gray-text); font-size: 0.9rem; margin-top: 5px; margin-bottom: 15px; }
        
        /* إحصائيات الحضور */
        .attendance-stat { text-align: center; padding: 20px 0; }
        .percentage { font-size: 3.5rem; font-weight: bold; color: var(--primary-green); margin: 0; }
        
        /* 🚀 1. شبكة المقررات الدراسية (تصميم جديد) */
        .courses-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; list-style: none; padding: 0; margin: 0; }
        @media (max-width: 500px) { .courses-grid { grid-template-columns: 1fr; } }
        .course-badge { background: var(--light-green); color: var(--primary-green); padding: 12px 20px; border-radius: 8px; font-weight: bold; display: flex; align-items: center; justify-content: space-between; border-right: 4px solid var(--primary-green); font-size: 0.95em; }
        .course-badge i { font-size: 1.2em; }

        /* 🚀 2. الجدول الدراسي (تصميم جديد) */
        .schedule-list { list-style: none; padding: 0; margin: 0; }
        .schedule-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f5f9; }
        .schedule-item:last-child { border-bottom: none; padding-bottom: 0; }
        .schedule-right { display: flex; align-items: center; gap: 20px; font-weight: bold; color: var(--dark-text); }
        .schedule-day { color: var(--primary-green); border-left: 2px solid var(--primary-green); padding-left: 20px; min-width: 60px; font-size: 1.05em; }
        .schedule-name { font-size: 1em; }
        .schedule-time { color: var(--gray-text); font-size: 0.9em; display: flex; align-items: center; gap: 8px; background: #f8fafc; padding: 6px 15px; border-radius: 20px; border: 1px solid #e2e8f0; }

        /* الإعلانات */
        .alert-box { padding: 15px; border-radius: 8px; border-right-width: 4px; border-right-style: solid; font-size: 0.9rem; margin-bottom: 10px; }
        .alert-box i { margin-left: 8px; }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-info">
            <div style="text-align: center;">
                <img src="/logo.png" alt="شعار جامعة أم درمان الأهلية">
                <p style="margin: 0; font-size: 0.85rem; font-weight: bold; color: var(--primary-green);">
                    كلية {{ auth()->user()->faculty }}<br>قسم {{ auth()->user()->department }}
                </p>
            </div>
            <div class="header-text">
                <h1>جامعة أم درمان الأهلية</h1>
            </div>
        </div>
        <div class="header-actions">
            <span style="font-weight: 500;">أهلاً بك، {{ explode(' ', auth()->user()->name)[0] }}</span>
            <a href="/logout" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> خروج</a>
        </div>
    </header>

    <div class="container">
        
        <!-- العمود الأيمن -->
        <div class="right-column">
            <div class="card profile-card">
                <img src="{{ auth()->user()->personal_photo ? asset(auth()->user()->personal_photo) : 'https://via.placeholder.com/100' }}" alt="صورة الطالب" class="profile-pic">
                <h2 class="profile-name">{{ auth()->user()->name }}</h2>
                <p class="profile-dept">الرقم الجامعي: {{ auth()->user()->university_id }}</p>
                <span style="background: var(--light-green); color: var(--primary-green); padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">طالب منتظم</span>
            </div>

            <div class="card">
                <h3 class="card-title"><i class="fa-solid fa-chart-pie"></i> إحصائيات الحضور</h3>
                <div class="attendance-stat">
                    <p class="percentage">{{ $attendancePercentage }}%</p>
                    <p style="color: var(--gray-text); margin: 5px 0 0 0;">نسبة المواظبة للمقررات</p>
                </div>
            </div>
        </div>

        <!-- العمود الأيسر -->
        <div class="left-column">
            
            <!-- 1. المقررات الدراسية (عرض الكروت) -->
            <div class="card">
                <h3 class="card-title"><i class="fa-solid fa-book-open"></i> المقررات الدراسية</h3>
                <ul class="courses-grid">
                    @forelse($courses as $course)
                        <li class="course-badge">
                            <span>{{ $course->course_name }}</span>
                            <i class="fa-solid {{ $course->icon ?? 'fa-laptop-code' }}"></i>
                        </li>
                    @empty
                        <li class="course-badge" style="grid-column: 1 / -1; justify-content: center; color: var(--gray-text); border-color: #cbd5e1; background: #f8fafc;">
                            لا توجد مقررات مسجلة لـ ({{ auth()->user()->faculty }} - {{ auth()->user()->department }}) حالياً.
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- 2. الجدول الدراسي (عرض القائمة المنظمة) -->
            <div class="card">
                <h3 class="card-title"><i class="fa-solid fa-calendar-days"></i> الجدول الدراسي</h3>
                <ul class="schedule-list">
                    @forelse($courses as $course)
                        @php
                            // هنا بنفصل اليوم عن الوقت (بشرط إنك تكون كاتبهم في الإدارة بينهم علامة - أو شرطة)
                            // مثال في الإدارة: "الأحد - 10:00 ص إلى 11:30 ص"
                            $dayTimeStr = $course->day_time ?? '';
                            $parts = explode('-', $dayTimeStr);
                            
                            $day = trim($parts[0] ?? 'غير محدد');
                            $time = trim($parts[1] ?? $dayTimeStr); // لو مفيش شرطة، هيعرض النص كله في الوقت
                        @endphp
                        
                        <li class="schedule-item">
                            <div class="schedule-right">
                                <span class="schedule-day">{{ $day }}</span>
                                <span class="schedule-name">{{ $course->course_name }}</span>
                            </div>
                            <div class="schedule-time" dir="ltr">
                                {{ $time }} <i class="fa-regular fa-clock"></i>
                            </div>
                        </li>
                    @empty
                        <li class="schedule-item" style="justify-content: center; color: var(--gray-text);">
                            لا يوجد جدول دراسي متاح حالياً.
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- 3. لوحة الإعلانات -->
            <div class="card">
                <h3 class="card-title"><i class="fa-solid fa-bullhorn"></i> لوحة الإعلانات</h3>
                
                @forelse($announcements as $announcement)
                    @php
                        $type = $announcement->type ?? '';
                        
                        if($type == 'warning') {
                            $bgStyle = 'background: #fff3cd; color: #856404; border-color: #ffeeba;';
                            $icon = 'fa-circle-exclamation';
                        } 
                        elseif($type == 'success') {
                            $bgStyle = 'background: var(--light-green); color: var(--primary-green); border-color: var(--primary-green);';
                            $icon = 'fa-check-circle';
                        } 
                        elseif($type == 'info') {
                            $bgStyle = 'background: #cce5ff; color: #004085; border-color: #b8daff;';
                            $icon = 'fa-circle-info';
                        } 
                        else {
                            $bgStyle = 'background: #e2e3e5; color: #383d41; border-color: #d6d8db;';
                            $icon = 'fa-bell';
                        }
                    @endphp

                    <div class="alert-box" style="{{ $bgStyle }}">
                        <strong><i class="fa-solid {{ $icon }}"></i> {{ $announcement->title }}:</strong>
                        {{ $announcement->content }}
                    </div>
                @empty
                    <div class="alert-box" style="background: #f8f9fa; color: var(--gray-text); border-color: #ddd; text-align: center;">
                        <i class="fa-solid fa-inbox"></i> لا توجد إعلانات جديدة في الوقت الحالي.
                    </div>
                @endforelse
            </div>
            
        </div>

    </div>

</body>
</html>