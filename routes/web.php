<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Alert;
use App\Models\Faculty; 
use App\Models\Course;  
use App\Models\Announcement; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController; 

Route::get('/', function () {
    return redirect('/login');
});

// ====================================================
// 1. مسارات تسجيل طالب جديد 
// ====================================================
Route::get('/register', function () {
    $faculties = Faculty::all();
    return view('register', compact('faculties'));
});

Route::post('/check-face', function (Request $request) {
    if (!$request->face_encoding) {
        return response()->json(['is_duplicate' => false]);
    }

    $parseEncoding = function($str) {
        $clean = str_replace(['[', ']', '"'], '', $str);
        $arr = explode(',', $clean);
        return count($arr) === 128 ? array_map('floatval', $arr) : null;
    };

    $newEncoding = $parseEncoding($request->face_encoding);
    
    if ($newEncoding) {
        $existingUsers = User::whereNotNull('face_encoding')->get(['name', 'face_encoding']);
        
        foreach ($existingUsers as $existingUser) {
            $dbEncoding = $parseEncoding($existingUser->face_encoding);
            if ($dbEncoding) {
                $distance = 0;
                for ($i = 0; $i < 128; $i++) {
                    $distance += pow($newEncoding[$i] - $dbEncoding[$i], 2);
                }
                $distance = sqrt($distance);
                
                if ($distance < 0.50) {
                    return response()->json(['is_duplicate' => true, 'name' => $existingUser->name]);
                }
            }
        }
    }
    return response()->json(['is_duplicate' => false]);
});

Route::post('/register', function (Request $request) {
    // 1. التحقق من التكرار وإرجاع خطأ JSON بدلاً من HTML
    $exists = User::where('email', $request->email)->orWhere('university_id', $request->university_id)->first();
    if($exists) {
        return response()->json([
            'errors' => ['general' => ['البريد الإلكتروني أو الرقم الجامعي مسجل مسبقاً! ❌']]
        ], 422);
    }

    if ($request->face_encoding) {
        $parseEncoding = function($str) {
            $clean = str_replace(['[', ']', '"'], '', $str);
            $arr = explode(',', $clean);
            return count($arr) === 128 ? array_map('floatval', $arr) : null;
        };

        $newEncoding = $parseEncoding($request->face_encoding);
        if ($newEncoding) {
            $existingUsers = User::whereNotNull('face_encoding')->get(['name', 'face_encoding']);
            foreach ($existingUsers as $existingUser) {
                $dbEncoding = $parseEncoding($existingUser->face_encoding);
                if ($dbEncoding) {
                    $distance = 0;
                    for ($i = 0; $i < 128; $i++) {
                        $distance += pow($newEncoding[$i] - $dbEncoding[$i], 2);
                    }
                    $distance = sqrt($distance);
                    if ($distance < 0.50) {
                        return response()->json([
                            'errors' => ['general' => ['❌ الوجه مسجل مسبقاً باسم: ' . $existingUser->name]]
                        ], 422);
                    }
                }
            }
        }
    }

    // 2. حفظ الطالب في قاعدة بيانات الحضور
    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->university_id = $request->university_id;
    $user->password = Hash::make($request->password);
    $user->faculty = $request->faculty;
    $user->department = $request->department;
    $user->face_encoding = $request->face_encoding;

    if ($request->hasFile('personal_photo')) {
        $file = $request->file('personal_photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/students'), $filename);
        $user->personal_photo = 'uploads/students/' . $filename;
    }

    $user->save();

    // =================================================================
    // 🚀 3. كود الربط: إرسال الطالب فوراً لمشروع النتائج (بورت 8000)
    // =================================================================
    try {
        \Illuminate\Support\Facades\Http::post('http://127.0.0.1:8000/api/sync-student', [
            'name'          => $user->name,
            'email'         => $user->email,
            'university_id' => $user->university_id,
            'faculty'       => $user->faculty,
            'department'    => $user->department,
        ]);
    } catch (\Exception $e) {
        // لو سيرفر النتائج مقفول، النظام هيكمل حفظ عادي ومش هيعطل الطالب
        \Log::error('فشل الاتصال بمشروع النتائج: ' . $e->getMessage());
    }

    // 4. إرجاع رسالة نجاح للمتصفح عشان يحول الطالب لصفحة الدخول
    return response()->json(['success' => true]);
});

// ====================================================
// 2. الواجهة الرئيسية
// ====================================================
Route::get('/login', function () {
    return view('login');
})->name('login');


// ====================================================
// 3. مسارات تسجيل دخول المشرف
// ====================================================
Route::get('/admin/login', function () {
    return view('ad.admin_login'); 
})->name('admin.login');

Route::post('/admin/login', function (Request $request) {
    $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);

    if ($request->email === 'admin@admin.com' && $request->password === '1234') {
        $admin = User::where('email', 'admin@admin.com')->first();
        if (!$admin) {
            $admin = new User();
            $admin->name = 'المدير العام';
            $admin->email = 'admin@admin.com';
            $admin->password = Hash::make('1234');
            $admin->university_id = 'admin_001';
            $admin->is_frozen = 0;
            $admin->save();
        }
        Auth::login($admin);
        $request->session()->regenerate();
        return redirect('/admin/dashboard');
    }

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $request->session()->regenerate();
        return redirect('/admin/dashboard');
    }
    return back()->withErrors(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة ❌']);
});


// ====================================================
// 4. مسار صفحة الحضور 
// ====================================================
Route::get('/attendance', function () {
    $students = User::whereNotNull('face_encoding')->get(['id', 'name', 'face_encoding', 'is_frozen', 'faculty', 'department']);
    return view('attendance', compact('students'));
});

Route::post('/face-login', function (Request $request) {
    $user = User::find($request->user_id);
    if ($user) {
        if ($user->is_frozen) {
            return response()->json(['success' => false, 'message' => 'account_frozen']);
        }
        Auth::login($user);
        $request->session()->regenerate();
        return response()->json(['success' => true, 'name' => $user->name]);
    }
    return response()->json(['success' => false, 'message' => 'not_found']);
});

// ====================================================
// 5. تسجيل الحضور وحفظ السجل
// ====================================================
Route::post('/save-student-attendance', function (Request $request) {
    $userId = $request->user_id;
    $isComplete = $request->is_complete;

    if ($isComplete) {
        Attendance::create([
            'user_id'  => $userId,
            'Date'     => now()->toDateString(), 
            'check_in' => now()->toTimeString(), 
            'Method'   => 'Face Recognition', 
            'Status'   => 'present' 
        ]);
    } else {
        Attendance::create([
            'user_id'  => $userId,
            'Date'     => now()->toDateString(), 
            'check_in' => now()->toTimeString(), 
            'Method'   => 'Face Recognition', 
            'Status'   => 'rejected' 
        ]);

        Alert::create([
            'user_id'     => $userId,
            'message'     => 'محاولة حضور فاشلة - عدم تطابق بصمة الوجه',
            'is_resolved' => false
        ]);
        
        $student = User::find($userId);
        if($student) {
            $student->is_frozen = 1;
            $student->save();
        }
    }
    return response()->json(['success' => true, 'message' => 'تم تسجيل العملية بنجاح']);
});


// ====================================================
// 6. تسجيل الخروج
// ====================================================
Route::get('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
});


// ====================================================
// 7. مسارات لوحة تحكم المشرف
// ====================================================
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/attendance', [AdminController::class, 'attendance']);
    Route::get('/students', [AdminController::class, 'students']);
    Route::get('/alerts', [AdminController::class, 'alerts']);
    
    // مسار إدارة المحتوى 
    Route::get('/content', [AdminController::class, 'content'])->name('admin.content');
    
    // مسارات الحفظ (POST)
    Route::post('/faculty/store', [AdminController::class, 'storeFaculty']);
    Route::post('/course/store', [AdminController::class, 'storeCourse']);
    Route::post('/announcement/store', [AdminController::class, 'storeAnnouncement']);
    
    // مسارات التحديث المباشر الجديدة
    Route::post('/faculty/update/{id}', [AdminController::class, 'updateFaculty']);
    Route::post('/course/update/{id}', [AdminController::class, 'updateCourse']); 
    Route::post('/announcement/update/{id}', [AdminController::class, 'updateAnnouncement'])->name('admin.announcement.update'); // 👈 تمت الإضافة هنا
    
    // مسارات الحذف 
    Route::post('/faculty/delete/{id}', [AdminController::class, 'deleteFaculty']);
    Route::post('/course/delete/{id}', [AdminController::class, 'deleteCourse']);
    Route::post('/announcement/delete/{id}', [AdminController::class, 'deleteAnnouncement']);
    Route::post('/student/delete/{id}', [AdminController::class, 'deleteStudent']);
    
    // مسارات التحديثات والتنبيهات
    Route::post('/student/update/{id}', [AdminController::class, 'updateStudent']);
    Route::post('/student/freeze/{id}', [AdminController::class, 'toggleFreeze']);
    Route::post('/alert/resolve/{id}', [AdminController::class, 'resolveAlert']);
    Route::post('/alerts/clear', [AdminController::class, 'clearAllAlerts']);
    Route::get('/latest-attendance-json', [AdminController::class, 'getLatestAttendanceJson'])->name('admin.latest_attendance');
    Route::post('/handle-verification', [AdminController::class, 'handleVerification']);
});

// ====================================================
// 8. مسار تجريبي
// ====================================================
Route::get('/test-attendance/{user_id}', function ($user_id) {
    $user = User::find($user_id);
    if (!$user) return "عفواً.. الطالب رقم ($user_id) غير موجود!";
    Attendance::create([
        'user_id'  => $user_id,          
        'Status'   => 'present',             
        'Date'     => now()->toDateString(), 
        'check_in' => now()->toTimeString(), 
        'Method'   => 'Test Mode'                  
    ]);
    return "تم التسجيل بنجاح!";
});

// ====================================================
// 9. مسار لوحة تحكم الطالب (Student Dashboard)
// ====================================================
Route::get('/student/dashboard', function () {
    if (!Auth::check() || Auth::user()->email === 'admin@admin.com') {
        return redirect('/login');
    }
    $user = Auth::user();
    $courses = \App\Models\Course::where('faculty', $user->faculty)
                                 ->where('department', $user->department)
                                 ->get();
    return view('student_dashboard', compact('courses'));
})->name('student.dashboard');

// ====================================================
// 9. مسار لوحة تحكم الطالب (Student Dashboard)
// ====================================================
Route::get('/student/dashboard', function () {
    // 🚀 التحسين: التحقق من الصلاحية باستخدام حقل role بدلاً من الإيميل
    if (!Auth::check() || Auth::user()->role === 'admin') {
        return redirect('/login');
    }
    
    // تعريف المتغير $user 
    $user = Auth::user();
    
    // 1. جلب المقررات بناءً على كلية وقسم الطالب
    $courses = \App\Models\Course::where('faculty', $user->faculty)
                                 ->where('department', $user->department)
                                 ->get();
                                 
    // 2. جلب جميع الإعلانات من الأحدث للأقدم (استخدام latest كطريقة أنظف)
    $announcements = \App\Models\Announcement::latest()->get();
    
    // 3. حساب نسبة الحضور الحقيقية للطالب
    $totalAttendances = \App\Models\Attendance::where('user_id', $user->id)->count();
    $presentAttendances = \App\Models\Attendance::where('user_id', $user->id)
                            ->whereIn('Status', ['present', 'تم القبول'])->count();
                            
    $attendancePercentage = $totalAttendances > 0 
        ? round(($presentAttendances / $totalAttendances) * 100) 
        : 100; // إذا لم يكن هناك محاضرات بعد، النسبة 100% افتراضياً

    // إرسال المتغيرات إلى الصفحة
    return view('student_dashboard', compact('user', 'courses', 'announcements', 'attendancePercentage'));
})->name('student.dashboard');