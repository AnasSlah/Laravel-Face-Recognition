<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // السماح بإضافة البيانات في جدول الحضور
    protected $fillable = [
        'user_id', 
        'Date', 
        'check_in', 
        'Method', 
        'Status'
    ];

    // ربط الحضور بالطالب (عشان دالة with('user') تشتغل)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}