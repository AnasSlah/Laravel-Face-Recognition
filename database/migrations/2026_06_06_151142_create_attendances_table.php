<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // ربط الحضور بحساب الطالب
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->date('date'); // تم تعديل الحرف ليكون صغير
            $table->time('check_in')->nullable();
            $table->string('method')->default('face_recognition'); // تم تعديل الحرف ليكون صغير
            $table->string('status')->default('تم القبول'); // لتطابق تصميم لوحة التحكم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};