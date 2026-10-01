@extends('ad.master')

@section('title', 'سجل الحضور اليومي')

@section('styles')
<style>
    /* العنوان */
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 50px; margin-bottom: 20px;
    }
    .page-header h1 { color: #1e293b; font-size: 2.2em; font-weight: 800; margin: 0; }

    /* الخط الفاصل */
    .header-divider { border: 0; height: 1px; background-color: #cbd5e1; margin-bottom: 30px; width: 100%; }

    /* شريط الفلاتر المودرن */
    .filter-bar {
        background: #ffffff; padding: 25px; border-radius: 20px; margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
        display: flex; gap: 20px; align-items: center; flex-wrap: wrap;
    }
    .filter-group { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 200px; }
    .filter-group label { color: #64748b; font-size: 0.9em; font-weight: 700; }
    .filter-group input, .filter-group select {
        background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;
        padding: 14px 15px; border-radius: 12px; outline: none; font-family: inherit; transition: all 0.3s ease;
    }
    .filter-group input:focus, .filter-group select:focus { 
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); background: #ffffff; 
    }

    /* ستايلات الجدول المودرن */
    .table-container {
        background: #ffffff; padding: 35px; border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); border: 1px solid #f8fafc; overflow-x: auto;
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; text-align: right; min-width: 900px; }
    .modern-table th { color: #64748b; font-weight: 700; padding: 0 20px 10px; font-size: 0.9em; text-transform: uppercase; }
    .modern-table tr { transition: all 0.3s ease; }
    .modern-table tbody tr { background: #f8fafc; }
    .modern-table tbody tr:hover { background: #f1f5f9; transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .modern-table td { padding: 16px 20px; color: #334155; border: none; vertical-align: middle; }
    .modern-table td:first-child { border-radius: 0 16px 16px 0; font-weight: 700; color: #475569; }
    .modern-table td:last-child { border-radius: 16px 0 0 16px; }

    /* معلومات الطالب */
    .student-info { display: flex; align-items: center; gap: 12px; }
    .student-avatar { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1em; overflow: hidden; }
    .avatar-blue { background: #e0f2fe; color: #0284c7; }
    .avatar-orange { background: #ffedd5; color: #ea580c; }

    /* الشارات (Badges) */
    .badge { padding: 6px 14px; border-radius: 30px; font-weight: 700; font-size: 0.85em; display: inline-flex; align-items: center; gap: 6px; }
    .badge-success { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
    .badge-danger { background: #ffe4e6; color: #e11d48; border: 1px solid #fecdd3; }
    
    .method-badge { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 8px; font-size: 0.85em; font-weight: 600; }
    
    /* شارة عدد المرات */
    .count-badge { background: #e0f2fe; color: #0284c7; padding: 3px 10px; border-radius: 12px; font-size: 0.8em; font-weight: 800; margin-top: 6px; display: inline-block; border: 1px solid #bae6fd; }

    /* تنسيق خاص للتاريخ والوقت */
    .time-block { display: flex; flex-direction: column; line-height: 1.4; align-items: flex-end; }
    .time-block .time { font-weight: 800; color: #0f172a; font-size: 1.05em; }
    .time-block .date { font-size: 0.85em; color: #94a3b8; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1>سجل الحضور والغياب 📅</h1>
    </div>

    <hr class="header-divider">

    <div class="filter-bar">
        <div class="filter-group">
            <label>البحث السريع 🔍</label>
            <input type="text" id="searchInput" placeholder="ابحث باسم الطالب أو الرقم الجامعي...">
        </div>
        <div class="filter-group">
            <label>تاريخ اليوم</label>
            <input type="date" id="dateFilter" value="{{ date('Y-m-d') }}">
        </div>
        <div class="filter-group">
            <label>الحالة</label>
            <select id="statusFilter">
                <option value="all">الكل</option>
                <option value="success">حاضر (تم القبول)</option>
                <option value="danger">مرفوض (محاولة تلاعب)</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>الرقم الجامعي</th>
                    <th>اسم الطالب</th>
                    <th>الكلية (القسم)</th>
                    <th>وقت أول حضور</th>
                    <th>طريقة التحقق</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody id="attendanceTableBody">
                {{-- 🚀 تجميع البيانات حسب الطالب عشان نعرض سطر واحد لكل طالب ونعد عدد مراته --}}
                @php
                    $groupedAttendances = $attendances->groupBy('user_id');
                @endphp

                @forelse($groupedAttendances as $userId => $userRecords)
                    @php 
                        // استخراج أول عملية حضور (أقدم سجل في اليوم ده للطالب)
                        $firstAttendance = $userRecords->sortBy('id')->first();
                        $student = $firstAttendance->user; 
                        $attendanceDate = $firstAttendance->Date ?? $firstAttendance->date ?? now();
                        $attendanceTime = $firstAttendance->check_in ?? $firstAttendance->Check_in ?? now();
                        $attendanceStatus = $firstAttendance->Status ?? $firstAttendance->status ?? 'absent';
                        
                        // عدد مرات الحضور
                        $checkinCount = $userRecords->count();
                    @endphp
                <tr class="attendance-row" data-date="{{ \Carbon\Carbon::parse($attendanceDate)->format('Y-m-d') }}">
                    <td>{{ $student->university_id ?? '---' }}</td>
                    <td>
                        <div class="student-info">
                            @if($student && !empty($student->personal_photo) && file_exists(public_path($student->personal_photo)))
                                <img src="{{ asset($student->personal_photo) }}" alt="صورة الطالب" class="student-avatar" style="object-fit: cover; border: 1px solid #e2e8f0;">
                            @else
                                <div class="student-avatar {{ $loop->iteration % 2 == 0 ? 'avatar-orange' : 'avatar-blue' }}">
                                    {{ mb_substr($student->name ?? 'ط', 0, 1) }}
                                </div>
                            @endif
                            <span style="font-weight: 700; color: #0f172a;" class="search-name">{{ $student->name ?? 'طالب غير معروف' }}</span>
                        </div>
                    </td>
                    <td style="color: #64748b; font-weight: 600;">
                        {{ $student->faculty ?? '—' }} 
                        @if($student && !empty($student->department))
                            ({{ $student->department }})
                        @endif
                    </td>
                    <td dir="ltr" style="font-weight: 600; color: #475569;">
                        <div class="time-block">
                            <span class="time">{{ \Carbon\Carbon::parse($attendanceTime)->format('h:i:s A') }}</span>
                            <span class="date">{{ \Carbon\Carbon::parse($attendanceDate)->format('d M Y') }}</span>
                            
                            {{-- 🚀 بادج يوضح عدد مرات الدخول لو كانت أكتر من مرة --}}
                            @if($checkinCount > 1)
                                <span class="count-badge">دخل {{ $checkinCount }} مرات اليوم</span>
                            @endif
                        </div>
                    </td>
                    <td><span class="method-badge">بصمة الوجه (AI)</span></td>
                    <td class="status-cell">
                        @if(in_array(strtolower($attendanceStatus), ['present', 'تم القبول']))
                            <span class="badge badge-success status-tag" data-status="success"><span>✅</span> حاضر</span>
                        @else
                            <span class="badge badge-danger status-tag" data-status="danger"><span>❌</span> مرفوض (تلاعب)</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #64748b; font-weight: 600;">📭 لا يوجد أي سجلات حضور مسجلة في النظام حتى الآن.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    // تفعيل فلاتر البحث
    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);
    document.getElementById('dateFilter').addEventListener('change', filterTable);

    function filterTable() {
        let searchValue = document.getElementById('searchInput').value.toLowerCase();
        let statusValue = document.getElementById('statusFilter').value;
        let dateValue = document.getElementById('dateFilter').value;
        
        let rows = document.querySelectorAll('.attendance-row');
        
        rows.forEach(row => {
            let textContent = row.innerText.toLowerCase();
            let statusTag = row.querySelector('.status-tag').getAttribute('data-status');
            let rowDate = row.getAttribute('data-date');
            
            let matchesSearch = textContent.includes(searchValue);
            let matchesStatus = (statusValue === 'all') || (statusTag === statusValue);
            
            let matchesDate = (!dateValue) || (rowDate === dateValue); 
            
            if(matchesSearch && matchesStatus && matchesDate) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // تشغيل الفلتر تلقائياً عند تحميل الصفحة
    window.onload = function() {
        filterTable();
    };

    // 🚀 التحديث التلقائي للجدول كل 5 ثواني 
    setInterval(() => {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                
                let newTbody = doc.getElementById('attendanceTableBody').innerHTML;
                let oldTbody = document.getElementById('attendanceTableBody').innerHTML;
                
                if (newTbody.trim() !== oldTbody.trim()) {
                    document.getElementById('attendanceTableBody').innerHTML = newTbody;
                    filterTable(); 
                }
            })
            .catch(error => console.error('Error auto-updating table:', error));
    }, 5000); 
</script>
@endsection