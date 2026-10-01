@extends('ad.master')

@section('title', 'إدارة المحتوى والمقررات')

@section('styles')
<style>
    /* ========================================== */
    /* العناوين والتقسيمات                        */
    /* ========================================== */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; margin-bottom: 15px; }
    .page-header h1, .page-header h2 { color: #1e293b; font-weight: 800; margin: 0; }
    .page-header h1 { font-size: 2.2em; }
    .page-header h2 { font-size: 1.8em; margin-top: 20px;}
    .header-divider { border: 0; height: 1px; background-color: #cbd5e1; margin-bottom: 25px; width: 100%; }

    /* ========================================== */
    /* شبكة التصميم (Layout)                       */
    /* ========================================== */
    .content-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 25px; align-items: start; margin-bottom: 40px; }

    /* ========================================== */
    /* كارت الإضافة (Form Card)                   */
    /* ========================================== */
    .form-card { background: #ffffff; padding: 25px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); border-top: 4px solid var(--primary-green); }
    .form-card.announcement-card { border-top-color: #f59e0b; } 
    .form-card.faculty-card { border-top-color: #3b82f6; } 
    .form-card h3 { margin-top: 0; color: #0f172a; font-weight: 800; margin-bottom: 20px; font-size: 1.3em;}
    
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 8px; color: #475569; font-weight: 700; font-size: 0.9em; }
    .form-control { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-family: inherit; transition: all 0.3s; box-sizing: border-box; }
    .form-control:focus { border-color: var(--primary-green); box-shadow: 0 0 0 3px rgba(16, 160, 74, 0.1); background: #ffffff; outline: none; }
    textarea.form-control { resize: vertical; min-height: 80px; }
    
    .btn-submit { background: linear-gradient(135deg, var(--primary-green) 0%, #047857 100%); color: white; border: none; padding: 12px; width: 100%; border-radius: 10px; font-weight: bold; font-size: 1.1em; cursor: pointer; transition: 0.3s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 8px;}
    .btn-submit.btn-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .btn-submit.btn-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
    .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

    /* ========================================== */
    /* الحاويات الديناميكية في الجانب الأيسر       */
    /* ========================================== */
    .dynamic-container { background: #ffffff; padding: 25px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); min-height: 380px; text-align: right; }
    .table-responsive-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; }
    
    /* ستايل الكليات */
    .faculty-vertical-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; margin-top: 20px; }
    .faculty-vertical-item { 
        background: #ffffff; padding: 20px; border-radius: 14px; 
        border: 1px solid #e2e8f0; border-right: 5px solid var(--primary-green); 
        display: flex; flex-direction: column; gap: 12px; 
        transition: all 0.3s; text-align: right; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .faculty-vertical-item:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-color: #cbd5e1; }
    .faculty-vertical-item h4 { margin: 0; color: #3b82f6; font-size: 1.2em; font-weight: 800; border-bottom: 1px dashed #e2e8f0; padding-bottom: 12px; }
    .depts-wrapper { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-start; align-items: center; }

    /* ستايل عرض الأقسام الديناميكي */
    .depts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-top: 20px; }
    .dept-card-click { background: #f0fdf4; border: 1px dashed #bbf7d0; padding: 15px; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.2s; color: #166534; font-weight: bold; }
    .dept-card-click:hover { background: var(--primary-green); color: white; border-style: solid; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(22, 101, 52, 0.2); }
    .dept-card-click i { display: block; font-size: 1.5em; margin-bottom: 8px; }

    /* ستايلات الجدول المودرن */
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; text-align: right; min-width: 550px; }
    .modern-table th { color: #64748b; font-weight: 700; padding: 0 15px 10px; font-size: 0.9em; white-space: nowrap; }
    .modern-table tbody tr { background: #f8fafc; transition: all 0.3s ease; }
    .modern-table tbody tr:hover { background: #f1f5f9; transform: scale(1.005); }
    .modern-table td { padding: 15px; color: #334155; border: none; vertical-align: middle; }
    .modern-table td:first-child { border-radius: 0 12px 12px 0; font-weight: bold; color: var(--primary-green); }
    .modern-table td:last-child { border-radius: 12px 0 0 12px; }

    /* أزرار الإجراءات */
    .action-btns button { background: #ffffff; border: 1px solid #e2e8f0; cursor: pointer; padding: 8px 12px; border-radius: 8px; transition: all 0.2s ease; color: #64748b; margin-left: 5px; }
    .btn-view:hover { color: #6366f1; border-color: #6366f1; background: #e0e7ff; }
    .btn-edit:hover { color: #3b82f6; border-color: #3b82f6; background: #eff6ff; }
    .btn-delete:hover { color: #ef4444; border-color: #ef4444; background: #fef2f2; }
    
    /* أزرار الحفظ والتجاهل للتعديل الفوري */
    .btn-save { color: #10b981; border-color: #10b981; background: #ffffff; font-weight: bold; }
    .btn-save:hover { color: white; background: #10b981; }
    .btn-cancel { color: #64748b; border-color: #64748b; background: #ffffff; font-weight: bold; }
    .btn-cancel:hover { color: white; background: #64748b; }

    .course-icon-preview { font-size: 1.2em; margin-left: 8px; color: #64748b; }

    /* شارات متنوعة */
    .announcement-badge { padding: 5px 10px; border-radius: 6px; font-size: 0.85em; font-weight: bold; display: inline-block;}
    .type-warning { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; } 
    .type-success { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; } 
    .type-info { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; } 
    .dept-badge { display: inline-block; background: #f1f5f9; color: #475569; padding: 5px 12px; border-radius: 8px; font-size: 0.85em; font-weight: 600; border: 1px solid #e2e8f0; }

    /* ========================================== */
    /* النافذة المنبثقة (Modal)                    */
    /* ========================================== */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 10px; box-sizing: border-box; }
    .modal-content { background-color: #ffffff; border-radius: 20px; padding: 30px; width: 100%; max-width: 700px; max-height: 85vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: fadeIn 0.3s ease-out; direction: rtl; box-sizing: border-box; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }
    .modal-header h3 { margin: 0; color: #0f172a; font-weight: 800; font-size: 1.4em; }
    .close-modal-btn { background: #f1f5f9; border: none; font-size: 1.2em; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; color: #64748b; transition: 0.2s; display: flex; justify-content: center; align-items: center;}
    .close-modal-btn:hover { color: white; background: #ef4444; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

    /* ========================================== */
    /* 📱 التحسينات الخاصة بالشاشات والهواتف الذكية */
    /* ========================================== */
    @media (max-width: 992px) { 
        .content-grid { grid-template-columns: 1fr; gap: 20px; } 
        .dynamic-container { min-height: auto; }
    }

    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: flex-start; gap: 10px; margin-top: 15px; }
        .page-header h1 { font-size: 1.6em; }
        .page-header h2 { font-size: 1.4em; margin-top: 10px; }
        
        .form-card, .dynamic-container { padding: 18px; border-radius: 16px; }
        .form-card h3, .dynamic-container h3 { font-size: 1.15em; }

        .faculty-vertical-list { grid-template-columns: 1fr; gap: 12px; }
        .depts-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; }
        .dept-card-click { padding: 10px; font-size: 0.9em; }

        .modern-table { min-width: 480px; font-size: 0.9em; }
        .modern-table th, .modern-table td { padding: 10px 8px; }

        .modal-content { padding: 20px; border-radius: 16px; max-height: 90vh; }
        .modal-header h3 { font-size: 1.1em; }

        /* ضبط مدخلات التعديل المباشر داخل الجداول للتكيف مع الهواتف */
        .modern-table .form-control {
            max-width: 100% !important;
            font-size: 0.85em !important;
            padding: 6px 8px !important;
        }

        .action-btns button, .btn-save, .btn-cancel {
            padding: 6px 8px !important;
            font-size: 0.8em !important;
        }
    }

    @media (max-width: 480px) {
        .page-header h1 { font-size: 1.35em; }
        .page-header h2 { font-size: 1.2em; }
        .btn-submit { font-size: 1em; padding: 10px; }
        .dept-badge { font-size: 0.75em; padding: 3px 8px; }
        .announcement-badge { font-size: 0.75em; padding: 3px 6px; }
    }
</style>
@endsection

@section('content')

    <!-- ========================================== -->
    <!-- القسم الأول: إدارة الكليات والأقسام        -->
    <!-- ========================================== -->
    <div class="page-header" style="margin-top: 10px;">
        <h1>إدارة الهيكل الأكاديمي 🏛️</h1>
    </div>
    <hr class="header-divider">

    <div class="content-grid">
        <div class="form-card faculty-card">
            <h3>✨ إضافة كلية جديدة</h3>
            <form id="addFacultyForm">
                <div class="form-group">
                    <label>اسم الكلية</label>
                    <input type="text" id="facName" class="form-control" placeholder="مثال: كلية الهندسة" required>
                </div>
                
                <div class="form-group">
                    <label>الأقسام التابعة لها</label>
                    <textarea id="facDepts" class="form-control" placeholder="اكتب الأقسام مفصولة بفاصلة (،) مثال: برمجيات، شبكات، اتصالات" required></textarea>
                    <small style="color: #94a3b8; font-size: 0.85em; display: block; margin-top: 5px;">* افصل بين كل قسم والآخر بفاصلة.</small>
                </div>

                <button type="button" id="btnSubmitFaculty" class="btn-submit btn-blue" onclick="saveData('/admin/faculty/store', {name: facName.value, departments: facDepts.value}, 'btnSubmitFaculty')">
                    <i class="fa-solid fa-building-columns"></i> إضافة الكلية
                </button>
            </form>
        </div>

        <div class="dynamic-container">
            <h3 style="margin-top: 0; color: #0f172a; font-weight: 800; margin-bottom: 20px;">الكليات والأقسام الحالية</h3>
            <div class="table-responsive-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr><th>الكلية</th><th>الأقسام التابعة لها</th><th style="text-align: center; width: 180px;">إجراءات</th></tr>
                    </thead>
                    <tbody>
                        @forelse($faculties as $fac)
                        <tr id="faculty-row-{{ $fac->id }}" data-departments="{{ $fac->departments }}">
                            <td style="color: #3b82f6;">
                                <span id="fac-name-text-{{ $fac->id }}">{{ $fac->name }}</span>
                                <input type="text" id="fac-name-input-{{ $fac->id }}" class="form-control" value="{{ $fac->name }}" style="display: none; padding: 6px 10px; font-size: 0.95em; max-width: 180px; margin: 0;">
                            </td>
                            <td>
                                @php $depts = array_filter(explode('،', str_replace(',', '،', $fac->departments))); @endphp
                                @foreach($depts as $dept)
                                    <span class="dept-badge">{{ trim($dept) }}</span>
                                @endforeach
                            </td>
                            <td style="white-space: nowrap; text-align: center;">
                                <div id="standard-actions-{{ $fac->id }}" style="display: inline-flex; gap: 5px;">
                                    <button class="btn-view" title="عرض الأقسام" onclick="viewFacultyDepts({{ $fac->id }}, '{{ $fac->name }}', '{{ $fac->departments }}')"><i class="fa-solid fa-eye"></i></button>
                                    <button class="btn-edit" title="تعديل الكلية" onclick="toggleEditFaculty({{ $fac->id }}, true)"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-delete" title="حذف الكلية" onclick="deleteData('/admin/faculty/delete/{{ $fac->id }}')"><i class="fa-solid fa-trash"></i></button>
                                </div>
                                <div id="edit-actions-{{ $fac->id }}" style="display: none; gap: 5px; justify-content: center;">
                                    <button id="btn-save-{{ $fac->id }}" class="btn-save" title="حفظ التعديل" onclick="saveInlineFaculty({{ $fac->id }})"><i class="fa-solid fa-check"></i> حفظ</button>
                                    <button class="btn-cancel" title="تجاهل" onclick="toggleEditFaculty({{ $fac->id }}, false)"><i class="fa-solid fa-xmark"></i> تجاهل</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 20px; color: #64748b;">لا توجد كليات مضافة حتى الآن.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- القسم الثاني: إدارة المقررات الدراسية     -->
    <!-- ========================================== -->
    <div class="page-header">
        <h2>إدارة المقررات الدراسية 📚</h2>
    </div>
    <hr class="header-divider">

    <div class="content-grid">
        <div class="form-card">
            <h3>✨ إضافة مقرر جديد</h3>
            <form id="addCourseForm">
                <div class="form-group">
                    <label>اسم المادة</label>
                    <input type="text" id="courseName" class="form-control" placeholder="مثال: هندسة البرمجيات" required>
                </div>
                <div class="form-group">
                    <label>الكلية</label>
                    <select class="form-control" id="courseFaculty" onchange="handleFacultySelection()" required>
                        <option value="">اختر الكلية...</option>
                        @foreach($faculties as $fac)
                            <option value="{{ $fac->name }}" data-depts="{{ $fac->departments }}">{{ $fac->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>القسم</label>
                    <select class="form-control" id="courseDept" onchange="handleDepartmentSelection()" required>
                        <option value="">اختر القسم أولاً...</option>
                    </select>
                </div>
                
                <!-- 🚀 التعديل هنا: تحويل الوقت لضوابط دقيقة (قائمة منسدلة لليوم + اختيار ساعة) -->
                <div class="form-group">
                    <label>يوم المحاضرة</label>
                    <select id="courseDay" class="form-control" required>
                        <option value="">اختر اليوم...</option>
                        <option value="السبت">السبت</option>
                        <option value="الأحد">الأحد</option>
                        <option value="الإثنين">الإثنين</option>
                        <option value="الثلاثاء">الثلاثاء</option>
                        <option value="الأربعاء">الأربعاء</option>
                        <option value="الخميس">الخميس</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label>من الساعة</label>
                        <input type="time" id="courseTimeFrom" class="form-control" required>
                    </div>
                    <div style="flex: 1;">
                        <label>إلى الساعة</label>
                        <input type="time" id="courseTimeTo" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>أيقونة المادة (FontAwesome)</label>
                    <input type="text" id="courseIcon" class="form-control" value="fa-book" placeholder="مثال: fa-book" required>
                </div>
                <!-- 🚀 الزرار اتغير عشان ينادي دالة بتجمع الوقت -->
                <button type="button" id="btnSubmitCourse" class="btn-submit" onclick="submitNewCourse()">
                    <i class="fa-solid fa-plus"></i> حفظ المقرر
                </button>
            </form>
        </div>

        <div class="dynamic-container" id="dynamicContentArea">
            <div id="viewInitialFaculties">
                <h3 style="margin-top: 0; color: #0f172a; font-weight: 800;">هيكل كليات النظام 🏛️</h3>
                <p style="color: #64748b; font-size: 0.9em; margin-bottom: 20px;">قم باختيار كلية من نموذج إضافة مقرر في القائمة المجاورة لعرض أقسامها ومقرراتها، أو تصفح الكليات المتاحة هنا:</p>
                <div class="faculty-vertical-list">
                    @forelse($faculties as $fac)
                    <div class="faculty-vertical-item">
                        <h4>{{ $fac->name }}</h4>
                        <div class="depts-wrapper">
                            @php $depts = array_filter(explode('،', str_replace(',', '،', $fac->departments))); @endphp
                            @foreach($depts as $dept)
                                <span class="dept-badge">{{ trim($dept) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="faculty-vertical-item" style="border-right-color: #cbd5e1; text-align: center;">
                        <p style="color: #64748b; margin: 0;">لا توجد بيانات متاحة حالياً.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div id="viewDepartmentsOnly" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; gap: 10px; flex-wrap: wrap;">
                    <h3 style="margin: 0; color: #1e293b;" id="txtSelectedFacultyName">اسم الكلية المختارة</h3>
                    <button type="button" style="background: #f1f5f9; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: bold; color: #475569;" onclick="resetToInitialView()">
                        <i class="fa-solid fa-arrow-right"></i> عودة للكل
                    </button>
                </div>
                <p style="color: #64748b; font-size: 0.9em; margin-top: 10px;">الرجاء اختيار القسم المطلوب لعرض جدول الدراسة الخاص به:</p>
                <div class="depts-grid" id="containerDynamicDepts"></div>
            </div>

            <div id="viewStudyTable" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 10px; flex-wrap: wrap;">
                    <div>
                        <h3 style="margin: 0; color: #0f172a; font-weight: 800;" id="txtTableTitle">جدول الدراسة الحالي</h3>
                        <span id="txtTableBreadcrumb" style="font-size: 0.85em; color: var(--primary-green); font-weight: bold;">الكلية / القسم</span>
                    </div>
                    <button type="button" style="background: #f1f5f9; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: bold; color: #475569;" onclick="backToDeptsView()">
                        <i class="fa-solid fa-arrow-right"></i> تغيير القسم
                    </button>
                </div>
                <div class="table-responsive-wrapper">
                    <table class="modern-table" id="mainCoursesTable">
                        <thead>
                            <tr><th>المادة</th><th>الموعد</th><th>إجراءات</th></tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr class="course-data-row" id="course-row-{{ $course->id }}" data-faculty="{{ $course->faculty }}" data-department="{{ $course->department }}">
                                <td>
                                    <div id="course-name-display-{{ $course->id }}" style="display: flex; align-items: center;">
                                        <i class="fa-solid {{ $course->icon ?? 'fa-book' }} course-icon-preview"></i>
                                        <span id="course-name-text-{{ $course->id }}">{{ $course->course_name }}</span>
                                    </div>
                                    <input type="text" id="course-name-input-{{ $course->id }}" class="form-control" value="{{ $course->course_name }}" style="display: none; padding: 6px 10px; font-size: 0.95em; max-width: 180px; margin: 0;">
                                </td>
                                
                                <!-- 🚀 التعديل هنا: التعديل المباشر أصبح بقوائم واختيار وقت أيضاً للاتساق -->
                                <td>
                                    <span id="course-time-text-{{ $course->id }}" style="display: inline-block;">{{ $course->day_time }}</span>
                                    
                                    <div id="course-time-inputs-{{ $course->id }}" style="display: none; flex-direction: column; gap: 5px;">
                                        <select id="course-day-input-{{ $course->id }}" class="form-control" style="padding: 6px; font-size: 0.85em;">
                                            <option value="">اليوم...</option>
                                            <option value="السبت">السبت</option>
                                            <option value="الأحد">الأحد</option>
                                            <option value="الإثنين">الإثنين</option>
                                            <option value="الثلاثاء">الثلاثاء</option>
                                            <option value="الأربعاء">الأربعاء</option>
                                            <option value="الخميس">الخميس</option>
                                        </select>
                                        <div style="display: flex; gap: 5px;">
                                            <input type="time" id="course-from-input-{{ $course->id }}" class="form-control" style="padding: 6px; font-size: 0.85em;" title="من الساعة">
                                            <input type="time" id="course-to-input-{{ $course->id }}" class="form-control" style="padding: 6px; font-size: 0.85em;" title="إلى الساعة">
                                        </div>
                                        <small style="color:#ef4444; font-size:0.75em;">* يجب إدخال اليوم والوقتين</small>
                                    </div>
                                </td>

                                <td class="action-btns" style="white-space: nowrap;">
                                    <div id="course-standard-actions-{{ $course->id }}" style="display: inline-flex; gap: 5px;">
                                        <button class="btn-edit" title="تعديل" onclick="toggleEditCourse({{ $course->id }}, true)"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-delete" title="حذف" onclick="deleteData('/admin/course/delete/{{ $course->id }}')"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                    <div id="course-edit-actions-{{ $course->id }}" style="display: none; gap: 5px;">
                                        <button class="btn-save" title="حفظ" onclick="saveInlineCourse({{ $course->id }})" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #10b981; color: #10b981; background: #fff; cursor: pointer; font-weight: bold;"><i class="fa-solid fa-check"></i> حفظ</button>
                                        <button class="btn-cancel" title="إلغاء" onclick="toggleEditCourse({{ $course->id }}, false)" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #64748b; color: #64748b; background: #fff; cursor: pointer; font-weight: bold;"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            <tr id="rowNoCoursesFound" style="display: none;">
                                <td colspan="3" style="text-align: center; padding: 40px; color: #ef4444; font-weight: bold; background: #fef2f2; border-radius: 12px;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2.3em; display: block; margin-bottom: 10px; color: #fca5a5;"></i> لا توجد مقررات مسجلة.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- القسم الثالث: لوحة الإعلانات               -->
    <!-- ========================================== -->
    <div class="page-header">
        <h2>لوحة الإعلانات والتنبيهات 📢</h2>
    </div>
    <hr class="header-divider">

    <div class="content-grid">
        <div class="form-card announcement-card">
            <h3>📝 نشر إعلان جديد</h3>
            <form id="addAnnouncementForm">
                <div class="form-group">
                    <label>عنوان الإعلان</label>
                    <input type="text" id="annTitle" class="form-control" placeholder="اكتب العنوان هنا..." required>
                </div>
                <div class="form-group">
                    <label>نوع الإعلان</label>
                    <select class="form-control" id="annType" required>
                        <option value="warning">تنبيه / تحذير (أصفر)</option>
                        <option value="success">نجاح / تأكيد (أخضر)</option>
                        <option value="info">معلومة عامة (أزرق)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>نص الإعلان</label>
                    <textarea id="annContent" class="form-control" placeholder="اكتب محتوى الإعلان..." required></textarea>
                </div>
                <button type="button" id="btnSubmitAnn" class="btn-submit btn-warning" onclick="saveData('/admin/announcement/store', {title: annTitle.value, type: annType.value, content: annContent.value}, 'btnSubmitAnn')">
                    <i class="fa-solid fa-bullhorn"></i> نشر الإعلان
                </button>
            </form>
        </div>

        <div class="dynamic-container">
            <h3 style="margin-top: 0; color: #0f172a; font-weight: 800; margin-bottom: 20px;">الإعلانات النشطة</h3>
            <div class="table-responsive-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr><th>النوع</th><th>العنوان</th><th>نص الإعلان</th><th>إجراءات</th></tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $ann)
                        <tr id="ann-row-{{ $ann->id }}">
                            <td>
                                <div id="ann-type-display-{{ $ann->id }}">
                                    @if($ann->type == 'warning') <span class="announcement-badge type-warning">تنبيه</span>
                                    @elseif($ann->type == 'success') <span class="announcement-badge type-success">تأكيد</span>
                                    @else <span class="announcement-badge type-info">معلومة</span> @endif
                                </div>
                                <select id="ann-type-input-{{ $ann->id }}" class="form-control" style="display: none; padding: 6px 10px; font-size: 0.9em;">
                                    <option value="warning" {{ $ann->type == 'warning' ? 'selected' : '' }}>تنبيه / تحذير (أصفر)</option>
                                    <option value="success" {{ $ann->type == 'success' ? 'selected' : '' }}>نجاح / تأكيد (أخضر)</option>
                                    <option value="info" {{ $ann->type == 'info' ? 'selected' : '' }}>معلومة عامة (أزرق)</option>
                                </select>
                            </td>

                            <td style="font-weight: bold; color: #0f172a;">
                                <span id="ann-title-text-{{ $ann->id }}">{{ $ann->title }}</span>
                                <input type="text" id="ann-title-input-{{ $ann->id }}" class="form-control" value="{{ $ann->title }}" style="display: none; padding: 6px 10px; font-size: 0.95em;">
                            </td>

                            <td style="color: #64748b; font-size: 0.9em; max-width: 300px;">
                                <span id="ann-content-text-{{ $ann->id }}">{{ $ann->content }}</span>
                                <textarea id="ann-content-input-{{ $ann->id }}" class="form-control" style="display: none; padding: 6px 10px; font-size: 0.95em; min-height: 50px; resize: vertical;">{{ $ann->content }}</textarea>
                            </td>

                            <td class="action-btns" style="white-space: nowrap;">
                                <div id="ann-standard-actions-{{ $ann->id }}" style="display: inline-flex; gap: 5px;">
                                    <button class="btn-edit" title="تعديل" onclick="toggleEditAnn({{ $ann->id }}, true)"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-delete" title="حذف" onclick="deleteData('/admin/announcement/delete/{{ $ann->id }}')"><i class="fa-solid fa-trash"></i></button>
                                </div>
                                <div id="ann-edit-actions-{{ $ann->id }}" style="display: none; gap: 5px;">
                                    <button id="btn-save-ann-{{ $ann->id }}" class="btn-save" title="حفظ" onclick="saveInlineAnn({{ $ann->id }})" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #10b981; color: #10b981; background: #fff; cursor: pointer; font-weight: bold;"><i class="fa-solid fa-check"></i> حفظ</button>
                                    <button class="btn-cancel" title="إلغاء" onclick="toggleEditAnn({{ $ann->id }}, false)" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #64748b; color: #64748b; background: #fff; cursor: pointer; font-weight: bold;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: #64748b;">لا توجد إعلانات نشطة.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- النافذة المنبثقة (Popup Modal) لعرض الأقسام -->
    <!-- ========================================== -->
    <div id="viewDeptModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalFacultyName">اسم الكلية</h3>
                <button class="close-modal-btn" onclick="closeDeptModal()"><i class="fa-solid fa-times"></i></button>
            </div>
            <div class="table-responsive-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr><th>اسم القسم</th><th style="text-align: left; width: 200px;">إجراءات القسم</th></tr>
                    </thead>
                    <tbody id="modalDeptsTableBody">
                        <!-- يتم حقن البيانات بالجافاسكربت -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ==========================================
    // دالة مساعدة لتنسيق الوقت بصيغة عربية
    // ==========================================
    function formatTimeAr(time24) {
        if (!time24) return '';
        let [hours, minutes] = time24.split(':');
        hours = parseInt(hours);
        let period = hours >= 12 ? 'م' : 'ص';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 يصبح 12
        hours = hours < 10 ? '0' + hours : hours;
        return `${hours}:${minutes} ${period}`;
    }

    // ==========================================
    // منطق التعديل المباشر (Inline Edit) للكلية
    // ==========================================
    function toggleEditFaculty(id, isEditing) {
        const textSpan = document.getElementById(`fac-name-text-${id}`);
        const inputField = document.getElementById(`fac-name-input-${id}`);
        const standardActions = document.getElementById(`standard-actions-${id}`);
        const editActions = document.getElementById(`edit-actions-${id}`);

        if (isEditing) {
            textSpan.style.display = 'none';
            inputField.style.display = 'inline-block';
            standardActions.style.display = 'none';
            editActions.style.display = 'inline-flex';
            inputField.focus();
        } else {
            textSpan.style.display = 'inline-block';
            inputField.style.display = 'none';
            standardActions.style.display = 'inline-flex';
            editActions.style.display = 'none';
            inputField.value = textSpan.innerText.trim();
        }
    }

    async function saveInlineFaculty(id) {
        const inputField = document.getElementById(`fac-name-input-${id}`);
        const row = document.getElementById(`faculty-row-${id}`);
        const currentDepts = row.getAttribute('data-departments');
        const newName = inputField.value.trim();

        if (!newName) {
            Swal.fire('تنبيه!', 'اسم الكلية لا يمكن أن يكون فارغاً', 'warning');
            return;
        }

        await saveData(`/admin/faculty/update/${id}`, { name: newName, departments: currentDepts }, `btn-save-${id}`);
    }


    // ==========================================
    // منطق نافذة العرض المنبثقة للأقسام (Modal)
    // ==========================================
    let activeFacultyId = null;
    let activeFacultyName = "";
    let activeFacultyDepts = [];

    function viewFacultyDepts(id, name, deptsString) {
        activeFacultyId = id;
        activeFacultyName = name;
        activeFacultyDepts = deptsString.replace(/,/g, '،').split('،').map(d => d.trim()).filter(d => d !== '');

        document.getElementById('modalFacultyName').innerText = `🏛️ كلية ${name} - الأقسام التابعة`;
        renderModalDeptsTable();
        document.getElementById('viewDeptModal').style.display = 'flex';
    }

    function closeDeptModal() {
        document.getElementById('viewDeptModal').style.display = 'none';
    }

    function renderModalDeptsTable() {
        const tbody = document.getElementById('modalDeptsTableBody');
        tbody.innerHTML = '';

        if (activeFacultyDepts.length === 0) {
            tbody.innerHTML = `<tr><td colspan="2" style="text-align:center; padding:20px; color:#64748b;">لا توجد أقسام مسجلة حالياً.</td></tr>`;
            return;
        }

        activeFacultyDepts.forEach((dept, idx) => {
            tbody.innerHTML += `
                <tr>
                    <td style="color: #1f2937;">
                        <span id="dept-name-text-${idx}" style="font-weight: bold;">${dept}</span>
                        <input type="text" id="dept-name-input-${idx}" class="form-control" value="${dept}" style="display: none; padding: 6px 10px; font-size: 0.95em; max-width: 180px; margin: 0;">
                    </td>
                    <td style="text-align: left; white-space: nowrap;">
                        <div id="dept-standard-actions-${idx}" class="action-btns" style="display: inline-flex; gap: 5px;">
                            <button class="btn-edit" title="تعديل اسم القسم" onclick="toggleEditDept(${idx}, true)"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-delete" title="حذف القسم" onclick="deleteDeptInline('${dept}', ${idx})"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <div id="dept-edit-actions-${idx}" style="display: none; gap: 5px;">
                            <button class="btn-save" title="حفظ التعديل" onclick="saveInlineDept(${idx})" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #10b981; color: #10b981; background: #fff; cursor: pointer; font-weight: bold;"><i class="fa-solid fa-check"></i> حفظ</button>
                            <button class="btn-cancel" title="تجاهل" onclick="toggleEditDept(${idx}, false)" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #64748b; color: #64748b; background: #fff; cursor: pointer; font-weight: bold;"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    function toggleEditDept(index, isEditing) {
        const textSpan = document.getElementById(`dept-name-text-${index}`);
        const inputField = document.getElementById(`dept-name-input-${index}`);
        const standardActions = document.getElementById(`dept-standard-actions-${index}`);
        const editActions = document.getElementById(`dept-edit-actions-${index}`);

        if (isEditing) {
            textSpan.style.display = 'none';
            inputField.style.display = 'inline-block';
            standardActions.style.display = 'none';
            editActions.style.display = 'inline-flex';
            inputField.focus();
        } else {
            textSpan.style.display = 'inline-block';
            inputField.style.display = 'none';
            standardActions.style.display = 'inline-flex';
            editActions.style.display = 'none';
            inputField.value = textSpan.innerText.trim();
        }
    }

    async function saveInlineDept(index) {
        const inputField = document.getElementById(`dept-name-input-${index}`);
        const newName = inputField.value.trim();

        if (!newName) {
            Swal.fire('تنبيه!', 'اسم القسم لا يمكن أن يكون فارغاً', 'warning');
            return;
        }

        activeFacultyDepts[index] = newName;
        await updateFacultyDeptsAPI();
    }

    function deleteDeptInline(deptName, index) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: `سيتم حذف قسم (${deptName}) نهائياً!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'حذف',
            cancelButtonText: 'إلغاء'
        }).then(async (result) => {
            if (result.isConfirmed) {
                activeFacultyDepts.splice(index, 1);
                await updateFacultyDeptsAPI();
            }
        });
    }

    async function updateFacultyDeptsAPI() {
        const updatedDeptsString = activeFacultyDepts.join('،');
        try {
            let res = await fetch(`/admin/faculty/update/${activeFacultyId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ name: activeFacultyName, departments: updatedDeptsString })
            });
            if(res.ok) {
                Swal.fire({ title: 'تمت العملية!', text: 'تم تحديث البيانات بنجاح.', icon: 'success', timer: 1200, showConfirmButton: false })
                .then(() => window.location.reload());
            } else {
                Swal.fire('خطأ!', 'فشل تحديث البيانات في السيرفر.', 'error');
            }
        } catch (e) {
            Swal.fire('خطأ في الاتصال!', 'تأكد من اتصالك بالسيرفر.', 'error');
        }
    }

    window.onclick = function(e) {
        let modal = document.getElementById('viewDeptModal');
        if (e.target == modal) closeDeptModal();
    }


    // ==========================================
    // منطق المقررات والتحكم العام بالنظام
    // ==========================================
    function handleFacultySelection() {
        let facultySelect = document.getElementById('courseFaculty');
        let deptSelect = document.getElementById('courseDept');
        let selectedOption = facultySelect.options[facultySelect.selectedIndex];
        let facultyName = facultySelect.value;

        if (!facultyName) { resetToInitialView(); return; }

        let deptsAttr = selectedOption.getAttribute('data-depts');
        deptSelect.innerHTML = '<option value="">اختر القسم المطلوب...</option>';
        
        let containerDepts = document.getElementById('containerDynamicDepts');
        let deptsHTML = ''; 

        document.getElementById('txtSelectedFacultyName').innerText = `🏛️ أقسام ${facultyName}`;

        if (deptsAttr) {
            let deptArray = deptsAttr.replace(/,/g, '،').split('،');
            deptArray.forEach(dept => {
                let cleanDept = dept.trim();
                if (cleanDept) {
                    deptSelect.innerHTML += `<option value="${cleanDept}">${cleanDept}</option>`;
                    deptsHTML += `
                        <div class="dept-card-click" onclick="clickDeptFromLeft('${cleanDept}')">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>${cleanDept}</span>
                        </div>
                    `;
                }
            });
        }
        
        containerDepts.innerHTML = deptsHTML || '<p style="color: #64748b; grid-column: 1 / -1; text-align:center;">لا توجد أقسام مسجلة.</p>';
        document.getElementById('viewInitialFaculties').style.display = 'none';
        document.getElementById('viewStudyTable').style.display = 'none';
        document.getElementById('viewDepartmentsOnly').style.display = 'block';
    }

    function handleDepartmentSelection() {
        let facultyName = document.getElementById('courseFaculty').value;
        let deptName = document.getElementById('courseDept').value;

        if (!deptName) {
            if(facultyName) {
                document.getElementById('viewInitialFaculties').style.display = 'none';
                document.getElementById('viewStudyTable').style.display = 'none';
                document.getElementById('viewDepartmentsOnly').style.display = 'block';
            } else { resetToInitialView(); }
            return;
        }

        let rows = document.querySelectorAll('.course-data-row');
        let matchedCount = 0;

        rows.forEach(row => {
            if (row.getAttribute('data-faculty') === facultyName && row.getAttribute('data-department') === deptName) {
                row.style.display = 'table-row'; matchedCount++;
            } else { row.style.display = 'none'; }
        });

        document.getElementById('rowNoCoursesFound').style.display = (matchedCount === 0) ? 'table-row' : 'none';
        document.getElementById('viewInitialFaculties').style.display = 'none';
        document.getElementById('viewDepartmentsOnly').style.display = 'none';
        document.getElementById('viewStudyTable').style.display = 'block';
        document.getElementById('txtTableBreadcrumb').innerText = `${facultyName} ⬅️ ${deptName}`;
    }

    function clickDeptFromLeft(deptName) {
        document.getElementById('courseDept').value = deptName;
        handleDepartmentSelection(); 
    }

    function resetToInitialView() {
        document.getElementById('addCourseForm').reset();
        document.getElementById('courseDept').innerHTML = '<option value="">اختر القسم أولاً...</option>';
        document.getElementById('viewInitialFaculties').style.display = 'block';
        document.getElementById('viewDepartmentsOnly').style.display = 'none';
        document.getElementById('viewStudyTable').style.display = 'none';
    }

    function backToDeptsView() {
        document.getElementById('courseDept').value = "";
        document.getElementById('viewInitialFaculties').style.display = 'none';
        document.getElementById('viewStudyTable').style.display = 'none';
        document.getElementById('viewDepartmentsOnly').style.display = 'block';
    }

    // 🚀 دالة إضافة مقرر جديد بالضوابط الجديدة
    function submitNewCourse() {
        const name = document.getElementById('courseName').value;
        const faculty = document.getElementById('courseFaculty').value;
        const dept = document.getElementById('courseDept').value;
        const icon = document.getElementById('courseIcon').value;
        
        const day = document.getElementById('courseDay').value;
        const timeFrom = document.getElementById('courseTimeFrom').value;
        const timeTo = document.getElementById('courseTimeTo').value;

        if(!name || !faculty || !dept || !day || !timeFrom || !timeTo || !icon) {
            Swal.fire('تنبيه!', 'يرجى إكمال جميع بيانات المقرر وأوقات المحاضرة.', 'warning');
            return;
        }

        // دمج اليوم والوقت بالصيغة المطلوبة
        const finalTimeStr = `${day} - ${formatTimeAr(timeFrom)} إلى ${formatTimeAr(timeTo)}`;

        saveData('/admin/course/store', {
            course_name: name,
            faculty: faculty,
            department: dept,
            day_time: finalTimeStr,
            icon: icon
        }, 'btnSubmitCourse');
    }

    // ==========================================
    // منطق التعديل المباشر (Inline Edit) للمقررات
    // ==========================================
    function toggleEditCourse(id, isEditing) {
        const nameDisplay = document.getElementById(`course-name-display-${id}`);
        const nameInput = document.getElementById(`course-name-input-${id}`);
        
        const timeText = document.getElementById(`course-time-text-${id}`);
        const timeInputs = document.getElementById(`course-time-inputs-${id}`);
        
        const standardActions = document.getElementById(`course-standard-actions-${id}`);
        const editActions = document.getElementById(`course-edit-actions-${id}`);

        if (isEditing) {
            nameDisplay.style.display = 'none';
            nameInput.style.display = 'inline-block';
            
            timeText.style.display = 'none';
            timeInputs.style.display = 'flex';
            
            standardActions.style.display = 'none';
            editActions.style.display = 'inline-flex';
            nameInput.focus();
        } else {
            nameDisplay.style.display = 'flex'; 
            nameInput.style.display = 'none';
            
            timeText.style.display = 'inline-block';
            timeInputs.style.display = 'none';
            
            standardActions.style.display = 'inline-flex';
            editActions.style.display = 'none';
            
            nameInput.value = document.getElementById(`course-name-text-${id}`).innerText.trim();
        }
    }

    async function saveInlineCourse(id) {
        const nameInput = document.getElementById(`course-name-input-${id}`);
        const dayInput = document.getElementById(`course-day-input-${id}`);
        const fromInput = document.getElementById(`course-from-input-${id}`);
        const toInput = document.getElementById(`course-to-input-${id}`);
        const row = document.getElementById(`course-row-${id}`);
        
        const newName = nameInput.value.trim();
        const newDay = dayInput.value;
        const newFrom = fromInput.value;
        const newTo = toInput.value;
        
        const faculty = row.getAttribute('data-faculty');
        const department = row.getAttribute('data-department');

        if (!newName || !newDay || !newFrom || !newTo) {
            Swal.fire('تنبيه!', 'يرجى إكمال اسم المقرر واختيار اليوم وأوقات البداية والنهاية.', 'warning');
            return;
        }

        const finalTimeStr = `${newDay} - ${formatTimeAr(newFrom)} إلى ${formatTimeAr(newTo)}`;

        await saveData(`/admin/course/update/${id}`, { 
            course_name: newName, 
            day_time: finalTimeStr,
            faculty: faculty,
            department: department
        }, `course-edit-actions-${id}`);
    }

    // ==========================================
    // منطق التعديل المباشر (Inline Edit) للإعلانات
    // ==========================================
    function toggleEditAnn(id, isEditing) {
        const typeDisplay = document.getElementById(`ann-type-display-${id}`);
        const typeInput = document.getElementById(`ann-type-input-${id}`);
        const titleText = document.getElementById(`ann-title-text-${id}`);
        const titleInput = document.getElementById(`ann-title-input-${id}`);
        const contentText = document.getElementById(`ann-content-text-${id}`);
        const contentInput = document.getElementById(`ann-content-input-${id}`);
        const standardActions = document.getElementById(`ann-standard-actions-${id}`);
        const editActions = document.getElementById(`ann-edit-actions-${id}`);

        if (isEditing) {
            typeDisplay.style.display = 'none';
            typeInput.style.display = 'inline-block';
            titleText.style.display = 'none';
            titleInput.style.display = 'inline-block';
            contentText.style.display = 'none';
            contentInput.style.display = 'inline-block';
            
            standardActions.style.display = 'none';
            editActions.style.display = 'inline-flex';
            titleInput.focus();
        } else {
            typeDisplay.style.display = 'block';
            typeInput.style.display = 'none';
            titleText.style.display = 'inline-block';
            titleInput.style.display = 'none';
            contentText.style.display = 'inline-block';
            contentInput.style.display = 'none';
            
            standardActions.style.display = 'inline-flex';
            editActions.style.display = 'none';
            
            titleInput.value = titleText.innerText.trim();
            contentInput.value = contentText.innerText.trim();
        }
    }

    async function saveInlineAnn(id) {
        const typeInput = document.getElementById(`ann-type-input-${id}`);
        const titleInput = document.getElementById(`ann-title-input-${id}`);
        const contentInput = document.getElementById(`ann-content-input-${id}`);
        
        const newType = typeInput.value;
        const newTitle = titleInput.value.trim();
        const newContent = contentInput.value.trim();

        if (!newTitle || !newContent) {
            Swal.fire('تنبيه!', 'لا يمكن ترك عنوان أو نص الإعلان فارغًا', 'warning');
            return;
        }

        await saveData(`/admin/announcement/update/${id}`, { 
            title: newTitle, 
            type: newType,
            content: newContent
        }, `btn-save-ann-${id}`);
    }

    // ==========================================
    // دوال الحفظ والحذف الأساسية عبر الـ Fetch API
    // ==========================================
    async function saveData(url, data, btnId) {
        let btn = document.getElementById(btnId);
        let originalBtnHtml = btn ? btn.innerHTML : '';
        if(btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; }

        try {
            let res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(data)
            });
            
            if (res.status === 422) {
                let errorData = await res.json();
                Swal.fire({ title: 'خطأ!', html: Object.values(errorData.errors).flat().join('<br>'), icon: 'error' });
                return;
            }

            let result = await res.json();
            if(res.ok && result.success !== false) {
                Swal.fire({ title: 'تم بنجاح!', text: 'تمت العملية بنجاح في النظام.', icon: 'success', timer: 1500, showConfirmButton: false })
                .then(() => window.location.reload());
            } else {
                Swal.fire('خطأ!', result.message || 'حدث خطأ بالسيرفر', 'error');
            }
        } catch(e) { 
            Swal.fire('خطأ!', 'حدثت مشكلة بالاتصال، تأكد من تسجيل دخولك.', 'error'); 
        } finally {
            if(btn) { btn.disabled = false; btn.innerHTML = originalBtnHtml; }
        }
    }

    function deleteData(url) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من تراجع عن الحذف!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    let res = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    if(res.ok) {
                        Swal.fire({ title: 'تم الحذف!', icon: 'success', timer: 1500, showConfirmButton: false })
                        .then(() => window.location.reload());
                    } else { Swal.fire('خطأ!', 'فشل الحذف من السيرفر.', 'error'); }
                } catch(e) { Swal.fire('خطأ!', 'حدثت مشكلة بالاتصال.', 'error'); }
            }
        });
    }
</script>

@endsection