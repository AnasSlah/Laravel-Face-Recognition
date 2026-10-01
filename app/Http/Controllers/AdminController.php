<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance; 
use App\Models\Alert;
use App\Models\Course; 
use App\Models\Faculty; 
use App\Models\Announcement; 
use Carbon\Carbon;

class AdminController extends Controller
{
    // ----------------------------------------------------
    // 1. لوحة القيادة (Dashboard)
    // ----------------------------------------------------
    public function dashboard() 
    {
        $studentsCount = User::count(); 
        
        $todayString = now()->format('Y-m-d');
        
        $todayAttendance = Attendance::where('Date', $todayString)
                                     ->whereIn('Status', ['present', 'تم القبول'])
                                     ->count();
                                     
        $alertsCount = Attendance::where('Date', $todayString)
                                 ->whereIn('Status', ['rejected', 'مرفوض'])
                                 ->count();

        $latestAttendances = Attendance::with('user')
                                       ->orderBy('id', 'desc')
                                       ->take(5)
                                       ->get();

        return view('ad.admin_dashboard', compact('studentsCount', 'todayAttendance', 'alertsCount', 'latestAttendances'));
    }

    // ----------------------------------------------------
    // 2. معالجة عمليات التحقق 
    // ----------------------------------------------------
    public function handleVerification(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'is_complete' => 'required|boolean'
        ]);

        $userId = $request->input('user_id');
        $isComplete = $request->input('is_complete');

        if ($isComplete) {
            Attendance::create([
                'user_id'  => $userId,
                'Date'     => now()->format('Y-m-d'),
                'check_in' => now()->format('H:i:s'),
                'Method'   => 'AI', 
                'Status'   => 'present' 
            ]);

            return response()->json([
                'success' => true,
                'status'  => 'success',
                'message' => 'تم تسجيل حضور الطالب بنجاح ومزامنة لوحة التحكم ✅'
            ]);
        } else {
            Attendance::create([
                'user_id'  => $userId,
                'Date'     => now()->format('Y-m-d'),
                'check_in' => now()->format('H:i:s'),
                'Method'   => 'AI', 
                'Status'   => 'rejected' 
            ]);

            Alert::create([
                'user_id'     => $userId,
                'type'        => 'حضور غير مكتمل',
                'details'     => 'فشل التحقق من الهوية الكاملة أثناء عملية البصمة الحيوية للوجه.',
                'is_resolved' => false
            ]);

            $student = User::find($userId);
            if($student) {
                $student->is_frozen = 1;
                $student->save();
            }

            return response()->json([
                'success' => true,
                'status'  => 'warning',
                'message' => 'لم يكتمل الفحص. تم تسجيل إنذار أمني في النظام ⚠️'
            ]);
        }
    }

    // ----------------------------------------------------
    // 3. إدارة الطلاب 
    // ----------------------------------------------------
    public function students() 
    {
        $students = User::orderBy('id', 'desc')->get();
        $faculties = Faculty::all(); // 🚀 تم جلب الكليات هنا لتعمل القوائم المنسدلة في Blade
        
        return view('ad.students_management', compact('students', 'faculties'));
    }

    public function updateStudent(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $id,
            'university_id' => 'required|string|unique:users,university_id,' . $id,
            'faculty'       => 'nullable|string|max:255',
            'department'    => 'nullable|string|max:255',
        ]);

        $student = User::findOrFail($id);
        
        $student->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'university_id' => $request->university_id,
            'faculty'       => $request->faculty,
            'department'    => $request->department,
        ]);

        return response()->json(['success' => true, 'message' => 'تم تحديث بيانات الطالب بنجاح ✅']);
    }

    public function toggleFreeze($id)
    {
        $student = User::findOrFail($id);
        $student->is_frozen = $student->is_frozen ? 0 : 1;
        $student->save();

        $msg = $student->is_frozen ? 'تم تجميد حساب الطالب وإيقافه ❄️' : 'تم إعادة تنشيط حساب الطالب بنجاح ✅';
        return response()->json(['success' => true, 'is_frozen' => $student->is_frozen ? 1 : 0, 'message' => $msg]);
    }

    public function deleteStudent($id)
    {
        $student = User::find($id);
        if (!$student) {
            return redirect()->back();
        }

        if ($student->personal_photo && file_exists(public_path($student->personal_photo))) {
            @unlink(public_path($student->personal_photo));
        }
        $student->delete();

        return redirect()->back()->with('success', 'تم حذف حساب الطالب نهائياً بنجاح! 🗑️');
    }

    // ----------------------------------------------------
    // 4. سجل الحضور الكامل
    // ----------------------------------------------------
    public function attendance() 
    {
        $attendances = Attendance::with('user')->orderBy('id', 'desc')->get();
        return view('ad.attendance_log', compact('attendances'));
    }

    // ----------------------------------------------------
    // 5. سجل التنبيهات
    // ----------------------------------------------------
    public function alerts() 
    {
        $alerts = Alert::with('user')->orderBy('id', 'desc')->get();
        return view('ad.alerts_log', compact('alerts'));
    }

    // ----------------------------------------------------
    // 6. التحديث التلقائي للداشبورد (AJAX)
    // ----------------------------------------------------
    public function getLatestAttendanceJson()
    {
        $latest = Attendance::with('user')->orderBy('id', 'desc')->first();
        
        $todayString = now()->format('Y-m-d');
        $studentsCount = User::count(); 
        $todayAttendance = Attendance::where('Date', $todayString)->whereIn('Status', ['present', 'تم القبول'])->count();
        $alertsCount = Attendance::where('Date', $todayString)->whereIn('Status', ['rejected', 'مرفوض'])->count();

        if ($latest) {
            $statusBadge = in_array(strtolower($latest->Status), ['present', 'تم القبول']) 
                ? '<span class="badge badge-success"><span>✅</span> تم القبول</span>' 
                : '<span class="badge badge-danger"><span>❌</span> مرفوض</span>';

            $faculty = $latest->user->faculty ?? '—';
            if(!empty($latest->user->department)) {
                $faculty .= ' / ' . $latest->user->department;
            }

            $time = Carbon::parse($latest->check_in)->format('h:i A');
            $date = Carbon::parse($latest->Date)->format('d M Y'); 

            $rowHtml = "
                <tr data-id='{$latest->id}' style='background: #f0fdf4; transition: background 2s ease;'>
                    <td>" . ($latest->user->name ?? 'طالب غير معروف') . "</td>
                    <td style='color: #64748b; font-weight: 600;'>{$faculty}</td>
                    <td dir='ltr'>
                        <div class='time-block'>
                            <span class='time'>{$time}</span>
                            <span class='date'>{$date}</span>
                        </div>
                    </td>
                    <td>{$statusBadge}</td>
                </tr>
            ";

            return response()->json([
                'latest_id'       => $latest->id,
                'student_name'    => $latest->user->name ?? 'طالب غير معروف',
                'student_faculty' => $latest->user->faculty ?? '—',
                'check_in_time'   => $time,
                'row_html'        => $rowHtml,
                'stats' => [
                    'studentsCount'   => $studentsCount,
                    'todayAttendance' => $todayAttendance,
                    'alertsCount'     => $alertsCount 
                ]
            ]);
        }

        return response()->json(['latest_id' => 0]);
    }

    // ----------------------------------------------------
    // 7. التحكم في التنبيهات
    // ----------------------------------------------------
    public function resolveAlert($id)
    {
        Attendance::where('user_id', $id)
                  ->whereIn('Status', ['rejected', 'مرفوض'])
                  ->update(['Status' => 'مرفوض (تمت المراجعة)']);

        return response()->json(['success' => true]);
    }

    public function clearAllAlerts()
    {
        Alert::truncate(); 
        return response()->json(['success' => true]);
    }

    // ----------------------------------------------------
    // 8. إدارة المحتوى (الكليات، المقررات، الإعلانات) 🚀
    // ----------------------------------------------------
    public function content() 
    {
        $faculties = Faculty::orderBy('id', 'desc')->get();
        $courses = Course::orderBy('faculty')->orderBy('department')->get();
        $announcements = Announcement::orderBy('id', 'desc')->get();
        
        return view('ad.content_management', compact('faculties', 'courses', 'announcements'));
    }

    public function storeFaculty(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'departments' => 'required|string'
        ]);

        $facultyName = trim($request->name);
        $newDepartments = trim($request->departments);

        $faculties = Faculty::all();
        
        $normalizeArabic = function($str) {
            $str = trim($str);
            $str = str_replace(['ة', 'أ', 'إ', 'آ', 'ي'], ['ه', 'ا', 'ا', 'ا', 'ى'], $str);
            return $str;
        };

        $normalizedInput = $normalizeArabic($facultyName);

        $faculty = $faculties->first(function($item) use ($normalizeArabic, $normalizedInput) {
            return $normalizeArabic($item->name) === $normalizedInput;
        });

        if ($faculty) {
            $existingDepts = explode('،', str_replace(',', '،', $faculty->departments));
            $newDeptsArray = explode('،', str_replace(',', '،', $newDepartments));
            
            $allDepts = array_merge($existingDepts, $newDeptsArray);
            $allDepts = array_map('trim', $allDepts);
            $allDepts = array_unique($allDepts);
            $allDepts = array_filter($allDepts);

            $faculty->departments = implode('، ', $allDepts);
            $faculty->save();

            return response()->json([
                'success' => true, 
                'message' => 'الكلية مسجلة مسبقاً.. تم دمج الأقسام الجديدة إليها بنجاح دون تكرار الصفوف 🔄'
            ]);
        } else {
            $newDeptsArray = explode('،', str_replace(',', '،', $newDepartments));
            $cleanedDepts = array_filter(array_map('trim', $newDeptsArray));

            Faculty::create([
                'name' => $facultyName,
                'departments' => implode('، ', $cleanedDepts)
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'تمت إضافة الكلية الجديدة وهيكلة أقسامها بنجاح 🏛️'
            ]);
        }
    }

   // 🚀 التحديث السحري (الاسم والأقسام) مع النقل التلقائي للطلاب والمقررات
   public function updateFaculty(Request $request, $id)
   {
        $request->validate([
            'name' => 'required|string',
            'departments' => 'required|string'
        ]);

        $faculty = Faculty::find($id);
        if (!$faculty) {
            return response()->json(['success' => false, 'message' => 'الكلية غير موجودة'], 404);
        }

        $oldName = $faculty->name;
        $newName = trim($request->name);

        // 1. تحديث اسم الكلية في جدول الطلاب والمقررات (التحديث التلقائي)
        if ($oldName !== $newName) {
            User::where('faculty', $oldName)->update(['faculty' => $newName]);
            Course::where('faculty', $oldName)->update(['faculty' => $newName]);
        }

        // 2. تحديث وتتبع أسماء الأقسام
        $oldDeptsArray = array_values(array_filter(array_map('trim', explode('،', str_replace(',', '،', $faculty->departments)))));
        $newDeptsArray = array_values(array_filter(array_map('trim', explode('،', str_replace(',', '،', $request->departments)))));

        // اكتشاف القسم اللي اتغير (لو ضغط تعديل على قسم محدد)
        if (count($oldDeptsArray) === count($newDeptsArray)) {
            for ($i = 0; $i < count($oldDeptsArray); $i++) {
                $oldDept = $oldDeptsArray[$i];
                $newDept = $newDeptsArray[$i];

                // لو لقى اسم القسم مختلف، هيحدثه فوراً للطلاب والمقررات
                if ($oldDept !== $newDept) {
                    User::where('faculty', $newName)->where('department', $oldDept)->update(['department' => $newDept]);
                    Course::where('faculty', $newName)->where('department', $oldDept)->update(['department' => $newDept]);
                }
            }
        }

        // 3. حفظ البيانات الجديدة في جدول الكليات
        $faculty->name = $newName;
        $faculty->departments = implode('، ', $newDeptsArray);
        $faculty->save();

        return response()->json(['success' => true, 'message' => 'تم تحديث البيانات وتطبيقها على الطلاب والمقررات تلقائياً 🔄✅']);
   }

   public function deleteFaculty($id)
   {
        $faculty = Faculty::find($id);
        if ($faculty) {
            $faculty->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'الكلية غير موجودة'], 404);
   }

    // --- دوال المقررات ---
    public function storeCourse(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string',
            'faculty' => 'required|string',
            'department' => 'required|string',
            'day_time' => 'required|string'
        ]);

        Course::create($request->all());

        return response()->json(['success' => true, 'message' => 'تمت إضافة المقرر بنجاح 📚']);
    }

    public function updateCourse(Request $request, $id)
    {
        $request->validate([
            'course_name' => 'required|string',
            'faculty'     => 'required|string',
            'department'  => 'required|string',
            'day_time'    => 'required|string'
        ]);

        $course = Course::find($id);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'المقرر غير موجود'], 404);
        }

        $course->update([
            'course_name' => trim($request->course_name),
            'faculty'     => trim($request->faculty),
            'department'  => trim($request->department),
            'day_time'    => trim($request->day_time)
        ]);

        return response()->json(['success' => true, 'message' => 'تم تحديث المقرر بنجاح ✏️']);
    }

    public function deleteCourse($id)
    {
        $course = Course::find($id);
        if ($course) {
            $course->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'المقرر غير موجود'], 404);
    }

    // --- دوال الإعلانات ---
    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'content' => 'required|string'
        ]);

        Announcement::create($request->all());

        return response()->json(['success' => true, 'message' => 'تم نشر الإعلان بنجاح 📢']);
    }

    public function deleteAnnouncement($id)
    {
        $announcement = Announcement::find($id);
        if ($announcement) {
            $announcement->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'الإعلان غير موجود'], 404);
    }
}