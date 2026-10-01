<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    // نفس السحر لقفل حماية لارافل لتسهيل وتسريع عمليات الإدخال
    protected $guarded = [];

    /**
     * علاقة التنبيه بالمستخدم (كل تنبيه قد يرتبط بطالب محدد، أو يكون فارغاً null)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}