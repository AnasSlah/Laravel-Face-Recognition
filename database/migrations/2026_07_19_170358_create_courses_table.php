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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_name'); // اسم المادة
            $table->string('faculty'); // الكلية التابعة ليها
            $table->string('department'); // القسم التابع ليه
            $table->string('day_time'); // موعد المحاضرة
            $table->string('icon')->default('fa-book'); // أيقونة المادة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};