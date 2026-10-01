@extends('ad.master') 
@section('title', 'الرئيسية')

@section('styles')
<style>
    /* تنسيق العنوان الرئيسي والفراغات حوله */
    .dashboard-header {
        margin-top: 50px; 
    }
    .dashboard-header h1 {
        color: #1e293b;
        font-size: 2.2em;
        font-weight: 800;
        margin-bottom: 5px;
    }

    /* الخط الفاصل */
    .header-divider {
        border: 0;
        height: 1px;
        background-color: #cbd5e1; 
        margin-top: 20px;
        margin-bottom: 40px;
        width: 100%;
    }

    /* شبكة الكروت الإحصائية */
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 30px; 
        margin-bottom: 50px; 
    }
    
    /* تصميم الكارت الفخم */
    .stat-card { 
        display: block; 
        text-decoration: none; 
        padding: 30px; 
        border-radius: 24px; 
        color: white; 
        position: relative; 
        overflow: hidden; 
        box-shadow: 0 10px 20px rgba(0,0,0,0.08); 
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        color: white; 
    }
    .stat-card::after { 
        content: ''; position: absolute; top: -30px; left: -30px; width: 150px; height: 150px; 
        background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%); 
        border-radius: 50%; pointer-events: none;
    }
    
    /* ألوان التدرجات للكروت */
    .card-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .card-green { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
    .card-red { background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%); }

    .stat-value { font-size: 3em; font-weight: 900; line-height: 1; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); }
    .stat-label { font-size: 1.1em; opacity: 0.9; font-weight: 500; letter-spacing: 0.5px; }
    .stat-icon { position: absolute; bottom: 20px; left: 20px; font-size: 4em; opacity: 0.2; }

    /* تصميم جدول العمليات */
    .table-container {
        background: #ffffff; 
        padding: 35px; 
        border-radius: 24px; 
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid #f8fafc;
        overflow-x: auto; 
    }
    .table-container h3 { color: #0f172a; margin-top: 0; font-size: 1.5em; margin-bottom: 25px; font-weight: 800; }
    
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; text-align: right; min-width: 700px; }
    .modern-table th { color: #64748b; font-weight: 700; padding: 0 20px 10px; font-size: 0.9em; text-transform: uppercase; }
    .modern-table tr { transition: all 0.3s ease; }
    .modern-table tbody tr { background: #f8fafc; }
    .modern-table tbody tr:hover { background: #f1f5f9; transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    
    .modern-table td { padding: 18px 20px; color: #334155; border: none; vertical-align: middle; }
    .modern-table td:first-child { border-radius: 0 16px 16px 0; font-weight: 700; color: #0f172a; }
    .modern-table td:last-child { border-radius: 16px 0 0 16px; }

    /* الشارات (Badges) */
    .badge { padding: 8px 16px; border-radius: 30px; font-weight: 800; font-size: 0.85em; display: inline-flex; align-items: center; gap: 6px; }
    .badge-success { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
    .badge-warning { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .badge-danger { background: #ffe4e6; color: #e11d48; border: 1px solid #fecdd3; } 

    /* تنسيق خاص للتاريخ والوقت */
    .time-block { display: flex; flex-direction: column; line-height: 1.4; }
    .time-block .time { font-weight: 700; color: #475569; }
    .time-block .date { font-size: 0.85em; color: #94a3b8; }
</style>
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>لوحة القيادة 🚀</h1>
        <p style="color: #64748b; font-size: 1.1em; margin: 0;">ملخص حالة النظام اليوم - {{ date('d / m / Y') }}</p>
    </div>

    <hr class="header-divider">

    <div class="stats-grid">
        <a href="/admin/students" class="stat-card card-blue">
            <span class="stat-icon">👥</span>
            <div class="stat-value" id="count-students">{{ $studentsCount }}</div> 
            <div class="stat-label">إجمالي الطلاب المسجلين</div>
        </a>
        
        <a href="/admin/attendance" class="stat-card card-green">
            <span class="stat-icon">✅</span>
            <div class="stat-value" id="count-attendance">{{ $todayAttendance }}</div> 
            <div class="stat-label">حاضرون اليوم</div>
        </a>
        
        <a href="/admin/alerts" class="stat-card card-red">
            <span class="stat-icon">⚠️</span>
            <div class="stat-value" id="count-alerts">{{ $alertsCount }}</div> 
            <div class="stat-label">تنببهات ومحاولات تلاعب</div>
        </a>
    </div>

    <div class="table-container">
        <h3>أحدث عمليات الحضور</h3>
        <table class="modern-table">
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>الكلية / القسم</th>
                    <th>التاريخ والوقت</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                {{-- 🚀 التعديل هنا: استخدام unique('user_id') لعرض أحدث سجل لكل طالب فقط --}}
                @forelse($latestAttendances->unique('user_id') as $attendance) 
                    <tr data-id="{{ $attendance->id }}">
                        <td>{{ $attendance->user->name ?? 'طالب غير معروف' }}</td>
                        <td style="color: #64748b; font-weight: 600;">
                            {{ $attendance->user->faculty ?? '—' }} 
                            @if(!empty($attendance->user->department))
                                / {{ $attendance->user->department }}
                            @endif
                        </td>
                        <td dir="ltr">
                            <div class="time-block">
                                <span class="time">{{ \Carbon\Carbon::parse($attendance->check_in ?? $attendance->Check_in)->format('h:i A') }}</span>
                                <span class="date">{{ \Carbon\Carbon::parse($attendance->Date ?? $attendance->date)->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td>
                            @if(in_array(strtolower($attendance->Status ?? $attendance->status), ['present', 'تم القبول']))
                                <span class="badge badge-success"><span>✅</span> تم القبول</span>
                            @else
                                <span class="badge badge-danger"><span>❌</span> مرفوض</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr id="empty-row">
                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 30px; font-weight: 600;">
                            📭 لا توجد عمليات حضور مسجلة حتى الآن.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    let lastCheckedId = {{ $latestAttendances->first() ? $latestAttendances->first()->id : 0 }};

    // فحص لايف كل 3 ثوانٍ وتحديث الجدول بدون تكرار للطلاب
    async function checkLiveAttendance() {
        try {
            let response = await fetch('{{ route("admin.latest_attendance") }}');
            if (!response.ok) return;
            let data = await response.json();

            // لو في تسجيل جديد
            if (data.latest_id && data.latest_id > lastCheckedId) {
                lastCheckedId = data.latest_id;

                let tbody = document.getElementById('attendance-table-body');
                let emptyRow = document.getElementById('empty-row');
                if (emptyRow) emptyRow.remove();

                // 🚀 تحويل الـ HTML القادم من السيرفر لعنصر نقدر نقرأ منه بيانات الطالب
                let tempDiv = document.createElement('tbody');
                tempDiv.innerHTML = data.row_html;
                let newRow = tempDiv.firstElementChild;
                
                if (newRow) {
                    // استخراج اسم الطالب من الخانة الأولى
                    let studentName = newRow.firstElementChild.innerText.trim();
                    
                    // 🚀 البحث عن اسم الطالب في الجدول الحالي.. لو موجود نمسح السطر القديم
                    let existingRows = tbody.querySelectorAll('tr');
                    existingRows.forEach(row => {
                        let nameCell = row.firstElementChild;
                        if (nameCell && nameCell.innerText.trim() === studentName) {
                            row.remove();
                        }
                    });

                    // إدراج السطر الجديد كأول سطر في الجدول (يحتوي على الوقت الأحدث)
                    tbody.insertAdjacentElement('afterbegin', newRow);
                }

                // تحديث كروت الأعداد فوق 
                if (data.stats) {
                    document.getElementById('count-students').innerText = data.stats.studentsCount;
                    document.getElementById('count-attendance').innerText = data.stats.todayAttendance;
                    document.getElementById('count-alerts').innerText = data.stats.alertsCount;
                }
            }
        } catch (error) {
            console.error("خطأ في جلب بيانات الحضور المباشر:", error);
        }
    }

    // تشغيل الفحص التلقائي
    setInterval(checkLiveAttendance, 3000);
</script>
@endsection