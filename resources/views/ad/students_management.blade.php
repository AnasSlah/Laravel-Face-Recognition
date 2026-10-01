@extends('ad.master')

@section('title', 'إدارة الطلاب')

@section('styles')
<style>
    /* العنوان وزر الإضافة */
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 50px; margin-bottom: 20px;
    }
    .page-header h1 { color: #1e293b; font-size: 2.2em; font-weight: 800; margin: 0; }

    .btn-add {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white; border: none; padding: 12px 24px;
        border-radius: 12px; font-weight: bold; cursor: pointer; transition: all 0.3s ease;
        text-decoration: none; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-add:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4); color: white; }

    /* الخط الفاصل */
    .header-divider { border: 0; height: 1px; background-color: #cbd5e1; margin-bottom: 30px; width: 100%; }

    /* شريط البحث المودرن */
    .search-bar {
        background: #ffffff; padding: 20px; border-radius: 16px; margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 15px;
    }
    .search-input {
        background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;
        padding: 14px 20px; border-radius: 12px; outline: none; font-family: inherit; width: 100%; max-width: 450px;
        transition: all 0.3s ease; font-size: 0.95em;
    }
    .search-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); background: #ffffff; }

    /* ستايلات الجدول المودرن */
    .table-container {
        background: #ffffff; padding: 35px; border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); border: 1px solid #f8fafc; overflow-x: auto;
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; text-align: right; min-width: 800px; }
    .modern-table th { color: #64748b; font-weight: 700; padding: 0 20px 10px; font-size: 0.9em; text-transform: uppercase; }
    .modern-table tr { transition: all 0.3s ease; }
    .modern-table tbody tr { background: #f8fafc; }
    .modern-table tbody tr:hover { background: #f1f5f9; transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .modern-table td { padding: 16px 20px; color: #334155; border: none; vertical-align: middle; }
    .modern-table td:first-child { border-radius: 0 16px 16px 0; }
    .modern-table td:last-child { border-radius: 16px 0 0 16px; }

    /* معلومات الطالب (Avatar) */
    .student-info { display: flex; align-items: center; gap: 15px; }
    
    .student-avatar {
        width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 1.2em; background: #e0f2fe; color: #0284c7; flex-shrink: 0;
        overflow: hidden;
    }
    .student-avatar img {
        width: 100%; height: 100%; object-fit: cover; border-radius: 12px;
    }
    
    .student-name { font-weight: 800; color: #0f172a; margin-bottom: 3px; }
    .student-email { font-size: 0.85em; color: #64748b; transition: all 0.3s ease; }

    /* الشارات (Badges) */
    .badge { padding: 6px 14px; border-radius: 30px; font-weight: 700; font-size: 0.85em; display: inline-flex; align-items: center; gap: 6px; }
    .badge-success { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
    .badge-warning { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }

    /* أزرار الإجراءات */
    .action-btns { white-space: nowrap; }
    .action-btns button {
        background: #ffffff; border: 1px solid #e2e8f0; cursor: pointer; padding: 8px; border-radius: 8px;
        margin-left: 5px; transition: all 0.2s ease; font-size: 1.1em; color: #64748b;
    }
    .action-btns button:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    
    .btn-edit:hover { color: #3b82f6; border-color: #3b82f6; background: #eff6ff; }
    .btn-freeze:hover { color: #0ea5e9; border-color: #0ea5e9; background: #f0f9ff; }
    .btn-delete:hover { color: #ef4444; border-color: #ef4444; background: #fef2f2; }
    .btn-save:hover { color: #10b981; border-color: #10b981; background: #d1fae5; }
    .btn-cancel:hover { color: #ef4444; border-color: #ef4444; background: #fef2f2; }

    .d-none { display: none !important; }
    .edit-input {
        width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px;
        outline: none; font-family: inherit; font-size: 0.95em; transition: all 0.3s ease;
        background-color: #fff;
    }
    .edit-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .input-sm { padding: 6px 10px; font-size: 0.85em; margin-top: 4px; }
    
    .frozen-account { opacity: 0.6; background-color: #f1f5f9 !important; }
    .frozen-email { text-decoration: line-through; color: #ef4444 !important; }

    /* 📸 ستايلات المودال (تكبير الصورة) */
    .clickable-img {
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .clickable-img:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .image-modal {
        display: none; position: fixed; z-index: 9999; left: 0; top: 0;
        width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(5px); justify-content: center; align-items: center; flex-direction: column;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .image-modal.show {
        display: flex; opacity: 1;
    }
    .modal-content {
        max-width: 90%; max-height: 80vh; border-radius: 16px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.4); object-fit: contain;
        border: 4px solid #ffffff;
    }
    .close-modal {
        position: absolute; top: 25px; right: 40px; color: #f1f5f9;
        font-size: 45px; font-weight: bold; cursor: pointer; transition: 0.3s;
    }
    .close-modal:hover, .close-modal:focus {
        color: #ef4444; text-decoration: none; cursor: pointer; transform: scale(1.1);
    }
    #modalCaption {
        margin-top: 15px; color: #ffffff; font-size: 1.2em; font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1>إدارة الطلاب 👥</h1>
        <a href="/register" class="btn-add"><span>➕</span> إضافة طالب جديد</a>
    </div>

    <hr class="header-divider">

    <div class="search-bar">
        <span style="font-size: 1.2em; color: #94a3b8;">🔍</span>
        <input type="text" class="search-input" placeholder="ابحث بالاسم، الرقم الجامعي، أو البريد الإلكتروني...">
    </div>

    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>الطالب</th>
                    <th>الرقم الجامعي</th>
                    <th>الكلية (القسم)</th>
                    <th>بصمة الوجه (AI)</th>
                    <th>تاريخ التسجيل</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <!-- 🚀 حماية إضافية (?? []) في حالة نسيان إرسال المتغير من الكنترولر -->
                @forelse($students ?? [] as $student)
                <tr id="row-{{ $student->id }}" class="{{ $student->is_frozen ? 'frozen-account' : '' }}">
                    <td>
                        <div class="student-info">
                            <div class="student-avatar">
                                @if(!empty($student->personal_photo) && file_exists(public_path($student->personal_photo)))
                                    <img src="{{ asset($student->personal_photo) }}" alt="صورة {{ $student->name }}" class="clickable-img" onclick="openImageModal(this.src, '{{ $student->name }}')">
                                @else
                                    {{ mb_substr($student->name ?? 'ط', 0, 1, 'UTF-8') }}
                                @endif
                            </div>

                            <div style="width: 100%;">
                                <div class="view-mode">
                                    <div class="student-name" id="name-display-{{ $student->id }}">{{ $student->name }}</div>
                                    <div class="student-email {{ $student->is_frozen ? 'frozen-email' : '' }}" id="email-display-{{ $student->id }}">
                                        {{ $student->is_frozen ? 'متوقف (حساب مجمد)' : ($student->email ?? 'لا يوجد بريد مسجل') }}
                                    </div>
                                </div>
                                <div class="edit-mode d-none">
                                    <input type="text" class="edit-input" id="input-name-{{ $student->id }}" value="{{ $student->name }}" placeholder="اسم الطالب">
                                    <input type="email" class="edit-input input-sm" id="input-email-{{ $student->id }}" value="{{ $student->email }}" placeholder="البريد الإلكتروني">
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    <td style="font-weight: 700; color: #475569;">
                        <span class="view-mode" id="uni-display-{{ $student->id }}">{{ $student->university_id }}</span>
                        <div class="edit-mode d-none">
                            <input type="text" class="edit-input" id="input-uni-{{ $student->id }}" value="{{ $student->university_id }}">
                        </div>
                    </td>
                    
                    <td style="color: #64748b; font-weight: 600;">
                        <div class="view-mode" id="faculty-display-{{ $student->id }}">
                            {{ $student->faculty ?? '—' }}
                            @if(!empty($student->department))
                                / {{ $student->department }}
                            @endif
                        </div>
                        
                        <!-- 🚀 حماية إضافية للقوائم المنسدلة -->
                        <div class="edit-mode d-none">
                            <select class="edit-input" id="input-faculty-{{ $student->id }}" onchange="updateDepartments({{ $student->id }})">
                                <option value="">اختر الكلية...</option>
                                @foreach($faculties ?? [] as $fac)
                                    <option value="{{ $fac->name }}" {{ $student->faculty == $fac->name ? 'selected' : '' }}>
                                        {{ $fac->name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <select class="edit-input input-sm" id="input-dept-{{ $student->id }}" data-current="{{ $student->department }}">
                                <option value="">اختر القسم...</option>
                            </select>
                        </div>
                    </td>
                    
                    <td>
                        @if(!empty($student->face_encoding))
                            <span class="badge badge-success"><span>✓</span> مسجلة</span>
                        @else
                            <span class="badge badge-warning"><span>⚠️</span> غير مسجلة</span>
                        @endif
                    </td>
                    
                    <td style="color: #64748b;" dir="ltr">{{ $student->created_at ? $student->created_at->format('d/m/Y') : '-' }}</td>
                    
                    <td class="action-btns">
                        <div class="view-mode">
                            <button class="btn-edit" title="تعديل" onclick="toggleEdit({{ $student->id }})">✏️</button>
                            <button class="btn-freeze" id="btn-freeze-{{ $student->id }}" 
                                    title="{{ $student->is_frozen ? 'تنشيط الحساب' : 'تجميد الحساب' }}" 
                                    style="{{ $student->is_frozen ? 'background: #e0f2fe; color: #0284c7;' : '' }}"
                                    onclick="freezeAccount({{ $student->id }})">❄️</button>
                            <button class="btn-delete" title="حذف" onclick="deleteStudent({{ $student->id }})">🗑️</button>
                        </div>
                        <div class="edit-mode d-none">
                            <button class="btn-save" title="حفظ التعديلات" onclick="saveChanges({{ $student->id }})">💾</button>
                            <button class="btn-cancel" title="إلغاء التعديل" onclick="toggleEdit({{ $student->id }})">❌</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8; font-weight: 600; padding: 40px;">
                        📭 لا يوجد طلاب مسجلين في النظام حتى الآن.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="close-modal">&times;</span>
        <img class="modal-content" id="expandedImg">
        <div id="modalCaption"></div>
    </div>
@endsection

@section('scripts')
<script>
    // --- كود تكبير الصورة ---
    function openImageModal(src, name) {
        const modal = document.getElementById('imageModal');
        const expandedImg = document.getElementById('expandedImg');
        const captionText = document.getElementById('modalCaption');
        
        modal.classList.add('show');
        expandedImg.src = src;
        captionText.innerHTML = name;
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.remove('show');
    }

    // --- 🚀 توليد قاموس الأقسام برمجياً داخل الـ Blade نفسه (مضاد للأخطاء) ---
    @php
        $uniStructure = [];
        if(isset($faculties)) {
            foreach($faculties as $fac) {
                $deptsArray = array_values(array_filter(array_map('trim', explode('،', str_replace(',', '،', $fac->departments)))));
                $uniStructure[$fac->name] = $deptsArray;
            }
        }
    @endphp
    const universityStructure = @json($uniStructure);

    function updateDepartments(id, preSelectedDept = null) {
        const facultySelect = document.getElementById(`input-faculty-${id}`);
        const deptSelect = document.getElementById(`input-dept-${id}`);
        const selectedFaculty = facultySelect.value;
        
        // تفريغ قائمة الأقسام أولاً
        deptSelect.innerHTML = '<option value="">اختر القسم...</option>';
        
        // تعبئة الأقسام الخاصة بالكلية المختارة من قاعدة البيانات
        if (selectedFaculty && universityStructure[selectedFaculty]) {
            universityStructure[selectedFaculty].forEach(dept => {
                const isSelected = (dept === preSelectedDept) ? 'selected' : '';
                deptSelect.innerHTML += `<option value="${dept}" ${isSelected}>${dept}</option>`;
            });
        }
    }

    // --- أكواد الإدارة ---
    function toggleEdit(id) {
        const row = document.getElementById(`row-${id}`);
        const viewModes = row.querySelectorAll('.view-mode');
        const editModes = row.querySelectorAll('.edit-mode');
        
        viewModes.forEach(el => el.classList.toggle('d-none'));
        editModes.forEach(el => el.classList.toggle('d-none'));

        // فتح التعديل وتحديث الأقسام التابعة لكلية الطالب
        const isEditing = !editModes[0].classList.contains('d-none');
        if (isEditing) {
            const currentDept = document.getElementById(`input-dept-${id}`).getAttribute('data-current');
            updateDepartments(id, currentDept);
        }
    }

    async function saveChanges(id) {
        const newName = document.getElementById(`input-name-${id}`).value;
        const newEmail = document.getElementById(`input-email-${id}`).value;
        const newUniId = document.getElementById(`input-uni-${id}`).value;
        const newFaculty = document.getElementById(`input-faculty-${id}`).value;
        const newDept = document.getElementById(`input-dept-${id}`).value;

        try {
            let response = await fetch(`/admin/student/update/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: newName,
                    email: newEmail,
                    university_id: newUniId,
                    faculty: newFaculty,
                    department: newDept
                })
            });

            let result = await response.json();

            if (response.ok && result.success) {
                document.getElementById(`name-display-${id}`).innerText = newName;
                
                const row = document.getElementById(`row-${id}`);
                if (!row.classList.contains('frozen-account')) {
                    document.getElementById(`email-display-${id}`).innerText = newEmail || 'لا يوجد بريد مسجل';
                }
                
                document.getElementById(`uni-display-${id}`).innerText = newUniId;
                
                let facultyText = newFaculty || '—';
                if(newDept) facultyText += ' / ' + newDept;
                document.getElementById(`faculty-display-${id}`).innerText = facultyText;

                document.getElementById(`input-dept-${id}`).setAttribute('data-current', newDept);

                toggleEdit(id);
                alert(result.message);
            } else {
                alert('حدث خطأ أثناء التحديث: قد يكون البريد الإلكتروني أو الرقم الجامعي مستخدم بالفعل.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال بالسيرفر.');
        }
    }

    async function freezeAccount(id) {
        if(confirm('هل أنت متأكد من تغيير حالة حساب هذا الطالب؟')) {
            const row = document.getElementById(`row-${id}`);
            const emailDisplay = document.getElementById(`email-display-${id}`);
            const freezeBtn = document.getElementById(`btn-freeze-${id}`);

            try {
                let response = await fetch(`/admin/student/freeze/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                let result = await response.json();

                if (response.ok && result.success) {
                    if (result.is_frozen == 1) {
                        row.classList.add('frozen-account');
                        emailDisplay.classList.add('frozen-email');
                        freezeBtn.style.background = '#e0f2fe';
                        freezeBtn.style.color = '#0284c7';
                        freezeBtn.title = "تنشيط الحساب";
                        emailDisplay.innerText = "متوقف (حساب مجمد)";
                    } else {
                        row.classList.remove('frozen-account');
                        emailDisplay.classList.remove('frozen-email');
                        freezeBtn.style.background = '#ffffff';
                        freezeBtn.style.color = '#64748b';
                        freezeBtn.title = "تجميد الحساب";
                        emailDisplay.innerText = document.getElementById(`input-email-${id}`).value || 'لا يوجد بريد مسجل';
                    }
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ في الاتصال بالسيرفر.');
            }
        }
    }

    async function deleteStudent(id) {
        if(confirm('هل أنت متأكد من حذف هذا الطالب نهائياً؟ لا يمكن التراجع عن هذه الخطوة!')) {
            try {
                let response = await fetch(`/admin/student/delete/${id}`, {
                    method: 'POST', 
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                let result = await response.json();

                if (response.ok && result.success) {
                    const row = document.getElementById(`row-${id}`);
                    if(row) {
                        row.style.transition = "all 0.5s ease";
                        row.style.opacity = "0";
                        row.style.transform = "translateX(-20px)";
                        
                        setTimeout(() => {
                            row.remove();
                        }, 500);
                    }
                    
                    alert(result.message);
                } else {
                    alert(result.message || 'حدث خطأ أثناء الحذف.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ في الاتصال بالسيرفر.');
            }
        }
    }
</script>
@endsection