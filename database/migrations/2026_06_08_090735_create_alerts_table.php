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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            // ربط التنبيه بجدول الطلاب (يقبل القيمة فارغة في حال لم يتم التعرف على الوجه)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); 
            $table->string('type'); // نوع التنبيه (مثال: محاولة تلاعب، فشل متكرر، غير مكتمل)
            $table->text('details')->nullable(); // تفاصيل إضافية عن التنبيه (جعلناه يقبل قيمة فارغة لتجنب الأخطاء)
            $table->boolean('is_resolved')->default(false); // حالة التنبيه: هل تمت مراجعته أم لا
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};