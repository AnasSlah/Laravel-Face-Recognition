<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    // اسم الجدول إذا كان مختلفاً (اختياري)
    // protected $table = 'announcements';

    // الحقول المسموح إضافتها (Fillable)
    protected $fillable = [
        'title',
        'type',
        'content',
    ];
}