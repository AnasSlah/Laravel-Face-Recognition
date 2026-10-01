@extends('ad.master')

@section('title', 'سجل الحسابات المرفوضة')

@section('styles')
<style>
    /* العنوان */
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 50px; margin-bottom: 20px;
    }
    .page-header h1 { color: #1e293b; font-size: 2.2em; font-weight: 800; margin: 0; display: flex; align-items: center; }
    
    .badge-new {
        background: #e11d48; color: white; padding: 4px 12px; border-radius: 20px;
        font-weight: bold; font-size: 0.45em; margin-right: 15px; vertical-align: middle;
        box-shadow: 0 4px 10px rgba(225, 29, 72, 0.3);
    }

    /* الخط الفاصل */
    .header-divider { border: 0; height: 1px; background-color: #cbd5e1; margin-bottom: 30px; width: 100%; }

    /* ستايلات الجدول المودرن */
    .table-container {
        background: #ffffff; padding: 35px; border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); border: 1px solid #f8fafc; overflow-x: auto;
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; text-align: right; min-width: 900px; }
    .modern-table th { color: #64748b; font-weight: 700; padding: 0 20px 10px; font-size: 0.9em; text-transform: uppercase; }
    .modern-table tr { transition: all 0.3s ease; }
    
    /* تمييز صفوف التنبيهات */
    .alert-danger-row td { background: #fff1f2; }
    .alert-warning-row td { background: #f8fafc; } 
    
    .modern-table tbody tr:hover { transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .modern-table td { padding: 18px 20px; color: #334155; border: none; vertical-align: middle; }
    .modern-table td:first-child { border-radius: 0 16px 16px 0; }
    .modern-table td:last-child { border-radius: 16px 0 0 16px; }

    /* تفاصيل الطالب */
    .student-name { font-weight: 800; color: #0f172a; margin-bottom: 3px; font-size: 1.05em; }
    .student-id { font-size: 0.85em; color: #64748b; font-weight: bold; }

    /* الشارات (Badges) */
    .badge { padding: 6px 14px; border-radius: 30px; font-weight: 700; font-size: 0.9em; display: inline-block; text-align: center; }
    .badge-danger { background: #ffe4e6; color: #e11d48; border: 1px solid #fecdd3; }
    .badge-warning { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }

    /* تنسيق خاص للتاريخ والوقت */
    .time-block { display: flex; flex-direction: column; line-height: 1.4; }
    .time-block .time { font-weight: 700; color: #475569; }
    .time-block .date { font-size: 0.85em; color: #94a3b8; }

    /* زر الإجراء */
    .btn-action {
        background: #ffffff; border: 1px solid #e2e8f0; color: #475569; padding: 8px 16px;
        border-radius: 8px; font-weight: 600; font-size: 0.9em; cursor: pointer; transition: all 0.2s ease;
    }
    .btn-action:hover { border-color: #10b981; color: #10b981; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.1); }
    .resolved-text { color: #10b981; font-weight: bold; display: flex; align-items: center; gap: 5px; }
</style>
@endsection

@section('content')
    @php 
        // 🚨 هنجيب كل المرفوضين (سواء راجعناهم أو لسه)
        $rejectedAttendances = \App\Models\Attendance::with('user')
                                ->whereIn('Status', ['rejected', 'مرفوض', 'مرفوض (تمت المراجعة)'])
                                ->orderBy('created_at', 'desc')
                                ->get();
                                
        $groupedRejections = $rejectedAttendances->groupBy('user_id');
        
        // 🚨 حساب عدد الحسابات اللي "لسه ماتراجعتش" عشان نظهرهم في العداد الأحمر فوق
        $unresolvedCount = $groupedRejections->filter(function($attempts) {
            return in_array($attempts->first()->Status, ['rejected', 'مرفوض']);
        })->count();
    @endphp

    <div class="page-header">
        <h1>سجل الحسابات المرفوضة والمشبوهة
            <span class="badge-new" id="alerts-counter" style="display: {{ $unresolvedCount > 0 ? 'inline-block' : 'none' }};">
                {{ $unresolvedCount }} حساب جديد
            </span>
        </h1>
    </div>

    <hr class="header-divider">

    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>الطالب / الحساب</th>
                    <th style="text-align: center;">محاولات الدخول الفاشلة</th>
                    <th>التفاصيل وحالة الحساب</th>
                    <th>توقيت آخر محاولة فاشلة</th>
                    <th>إجراءات المشرف</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groupedRejections as $userId => $attempts)
                    @php
                        $latestAttempt = $attempts->first(); 
                        $student = $latestAttempt->user; 
                        $failedCount = $attempts->count(); 
                        
                        // 🚨 التحقق إذا كان المشرف قام بمراجعة هذا الحساب مسبقاً
                        $isResolved = $latestAttempt->Status === 'مرفوض (تمت المراجعة)';
                    @endphp
                    
                    <tr id="row-{{ $userId }}" class="{{ $isResolved ? 'alert-warning-row' : 'alert-danger-row' }}">
                        
                        <td>
                            <div class="student-name">{{ $student->name ?? 'طالب غير معروف' }}</div>
                            <div class="student-id">الرقم الجامعي: {{ $student->university_id ?? '---' }}</div>
                        </td>

                        <td style="text-align: center;">
                            <span class="badge {{ $failedCount >= 3 ? 'badge-danger' : 'badge-warning' }}">
                                {{ $failedCount }} محاولات فاشلة
                            </span>
                        </td>

                        <td>
                            <div style="color: #e11d48; font-weight: 800; margin-bottom: 4px;">
                                <span style="font-size: 1.1em;">🚨</span> عدم تطابق بصمة الوجه
                            </div>
                            @if($student && $student->is_frozen)
                                <span style="font-size: 0.85em; color: #64748b; font-weight: 600;">(الحساب مجمد أمنياً ❄️)</span>
                            @else
                                <span style="font-size: 0.85em; color: #10b981; font-weight: 600;">(الحساب نشط ✅)</span>
                            @endif
                        </td>

                        <td dir="ltr">
                            <div class="time-block">
                                <span class="time">{{ \Carbon\Carbon::parse($latestAttempt->check_in)->format('h:i A') }}</span>
                                <span class="date">{{ \Carbon\Carbon::parse($latestAttempt->Date)->format('d M Y') }}</span>
                            </div>
                        </td>

                        <td>
                            @if($isResolved)
                                <span class="resolved-text"><span>✅</span> تمت المعالجة</span>
                            @else
                                <button class="btn-action" onclick="resolveAlert({{ $userId }})">مراجعة وتجاهل ✓</button>
                            @endif
                        </td>
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; font-weight: 600; padding: 40px;">
                            🎉 النظام آمن! لا توجد أي محاولات دخول مرفوضة حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    async function resolveAlert(userId) {
        try {
            // نداء للكنترولر عشان يحفظ المعالجة في الداتابيز
            let response = await fetch(`/admin/alert/resolve/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            let data = await response.json();
            
            if (data.success) {
                let row = document.getElementById(`row-${userId}`);
                row.classList.remove('alert-danger-row');
                row.classList.add('alert-warning-row');
                
                // تحويل الزر لعلامة تمت المعالجة
                row.querySelector('.btn-action').outerHTML = '<span class="resolved-text"><span>✅</span> تمت المعالجة</span>';
                
                // تقليل العداد
                let counter = document.getElementById('alerts-counter');
                let currentCount = parseInt(counter.innerText);
                if(currentCount > 1) {
                    counter.innerText = (currentCount - 1) + ' حساب جديد';
                } else {
                    counter.style.display = 'none'; 
                }
            }
        } catch (error) {
            alert('حدث خطأ في الاتصال بالخادم!');
        }
    }
</script>
@endsection