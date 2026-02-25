<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add homeroom_teacher_id to classes
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedInteger('homeroom_teacher_id')->nullable()->after('capacity');
            $table->foreign('homeroom_teacher_id', 'fk_class_homeroom_teacher')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');
        });

        // Add parent_id to lesson_periods for grouping 7-day periods
        Schema::table('lesson_periods', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->nullable()->after('semester_id');
            $table->foreign('parent_id', 'fk_period_parent')
                ->references('id')->on('lesson_periods')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign('fk_class_homeroom_teacher');
            $table->dropColumn('homeroom_teacher_id');
        });

        Schema::table('lesson_periods', function (Blueprint $table) {
            $table->dropForeign('fk_period_parent');
            $table->dropColumn('parent_id');
        });
    }
};
