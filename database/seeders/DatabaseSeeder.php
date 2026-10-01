<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course; // 🚀 استدعاء موديل المقررات

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 🚀 مقررات كلية الهندسة - قسم هندسة برمجيات
        Course::create([
            'course_name' => 'برمجة وتطوير الويب',
            'faculty' => 'الهندسة',
            'department' => 'هندسة برمجيات',
            'day_time' => 'الأحد - 10:00 صباحاً',
            'icon' => 'fa-code'
        ]);

        Course::create([
            'course_name' => 'قواعد البيانات المتقدمة',
            'faculty' => 'الهندسة',
            'department' => 'هندسة برمجيات',
            'day_time' => 'الإثنين - 08:00 صباحاً',
            'icon' => 'fa-database'
        ]);

        Course::create([
            'course_name' => 'الذكاء الاصطناعي',
            'faculty' => 'الهندسة',
            'department' => 'هندسة برمجيات',
            'day_time' => 'الخميس - 10:00 صباحاً',
            'icon' => 'fa-robot'
        ]);

        // 🚀 مقررات كلية التجارة - قسم محاسبة
        Course::create([
            'course_name' => 'مبادئ المحاسبة المالية',
            'faculty' => 'التجارة',
            'department' => 'محاسبة',
            'day_time' => 'الثلاثاء - 09:00 صباحاً',
            'icon' => 'fa-calculator'
        ]);

        Course::create([
            'course_name' => 'اقتصاد جزئي',
            'faculty' => 'التجارة',
            'department' => 'محاسبة',
            'day_time' => 'الأربعاء - 12:00 ظهراً',
            'icon' => 'fa-chart-pie'
        ]);
    }
}