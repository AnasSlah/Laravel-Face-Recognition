<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // 🚀 السماح بإضافة البيانات في هذه الأعمدة
    protected $fillable = [
        'course_name', 
        'faculty', 
        'department', 
        'day_time', 
        'icon'
    ];
}