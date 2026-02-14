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
        // majors
        Schema::create('majors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->unique();
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // grades
        Schema::create('grades', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
        });

        // subjects
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->unique();
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // academic_semesters
        Schema::create('academic_semesters', function (Blueprint $table) {
            $table->increments('id');
            $table->char('academic_year', 9);
            $table->tinyInteger('semester');
            $table->unique(['academic_year', 'semester']);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // classes
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->unique();
            $table->unsignedInteger('major_id');
            $table->unsignedInteger('grade_id');
            $table->integer('capacity')->default(50);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('major_id')
                  ->references('id')->on('majors')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('grade_id')
                  ->references('id')->on('grades')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // majors_subjects (Pivot)
        Schema::create('majors_subjects', function (Blueprint $table) {
            $table->unsignedInteger('major_id');
            $table->unsignedInteger('subject_id');
            $table->primary(['major_id', 'subject_id']);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('major_id')
                  ->references('id')->on('majors')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('subject_id')
                  ->references('id')->on('subjects')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // grades_subjects (Pivot)
        Schema::create('grades_subjects', function (Blueprint $table) {
            $table->unsignedInteger('grade_id');
            $table->unsignedInteger('subject_id');
            $table->primary(['grade_id', 'subject_id']);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('grade_id')
                  ->references('id')->on('grades')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('subject_id')
                  ->references('id')->on('subjects')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // lesson_periods
        Schema::create('lesson_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('weekday');
            $table->time('time_begin');
            $table->time('time_end');
            $table->unsignedInteger('semester_id');
            $table->unique(['weekday', 'time_begin', 'time_end', 'semester_id'], 'unique_period_per_semester');
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('semester_id')
                  ->references('id')->on('academic_semesters')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // activities
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('period_id');
            $table->unsignedInteger('class_id');
            $table->unique(['subject_id', 'teacher_id', 'period_id', 'class_id'], 'unique_activity');
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('subject_id')
                  ->references('id')->on('subjects')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('teacher_id')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('period_id')
                  ->references('id')->on('lesson_periods')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('class_id')
                  ->references('id')->on('classes')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // activity_presences
        Schema::create('activity_presences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('activity_id');
            $table->unsignedInteger('student_id');
            $table->date('activity_date')->default(date_default_timezone_get());
            $table->tinyInteger('score');
            $table->unique(['activity_id', 'student_id', 'activity_date'], 'unique_presence');
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('activity_id')
                  ->references('id')->on('activities')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('student_id')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // activity_reports
        Schema::create('activity_reports', function (Blueprint $table) {
            $table->unsignedInteger('presence_id')->primary();
            $table->tinyInteger('score');
            $table->string('topic', 255);
            $table->text('details');
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('presence_id')
                  ->references('id')->on('activity_presences')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all, but reverse the order. rGV????
        Schema::dropIfExists('activity_reports');
        Schema::dropIfExists('activity_presences');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('lesson_periods');
        Schema::dropIfExists('academic_semesters');
        Schema::dropIfExists('grades_subjects');
        Schema::dropIfExists('majors_subjects');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('majors');
    }
};