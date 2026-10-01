<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'university_id',
        'password',
        'faculty',
        'department',
        'face_encoding',
        'is_frozen', 
        'personal_photo', // 👈 تم إضافة الحقل هنا للسماح بحفظ صورة الطالب
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل نوع البيانات تلقائياً عند استدعائها في الكود
     */
    protected $casts = [
        'is_frozen' => 'boolean',
    ];

    /**
     * العلاقة بين الطالب والحضور: الطالب الواحد له عدة سجلات حضور
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    /**
     * العلاقة بين الطالب والتنبيهات: الطالب الواحد قد يكون له عدة تنبيهات ومحاولات تلاعب
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class, 'user_id');
    }
}