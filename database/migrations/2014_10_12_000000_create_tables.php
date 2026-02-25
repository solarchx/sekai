<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Majors
        Schema::create('majors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        // Grades
        Schema::create('grades', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->softDeletes();
            $table->timestamps();
        });

        // Subjects
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        // Academic Semesters
        Schema::create('academic_semesters', function (Blueprint $table) {
            $table->increments('id');
            $table->char('academic_year', 9);
            $table->tinyInteger('semester');
            $table->unique(['academic_year', 'semester']);
            $table->softDeletes();
            $table->timestamps();
        });

        // Classes
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->unique();
            $table->unsignedInteger('major_id');
            $table->unsignedInteger('grade_id');
            $table->integer('capacity')->default(50);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('major_id', 'fk_class_id_major')
                ->references('id')->on('majors')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('grade_id', 'fk_class_id_grade')
                ->references('id')->on('grades')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Users
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('identifier')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['STUDENT', 'TEACHER', 'VP', 'ADMIN']);
            $table->unsignedInteger('class_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('class_id', 'fk_student_class')
                ->references('id')->on('classes')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Majors Subjects (Pivot)
        Schema::create('majors_subjects', function (Blueprint $table) {
            $table->unsignedInteger('major_id');
            $table->unsignedInteger('subject_id');
            $table->primary(['major_id', 'subject_id']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('major_id', 'fk_majors_subjects_id_major')
                ->references('id')->on('majors')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('subject_id', 'fk_majors_subjects_id_subject')
                ->references('id')->on('subjects')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Grades Subjects (Pivot)
        Schema::create('grades_subjects', function (Blueprint $table) {
            $table->unsignedInteger('grade_id');
            $table->unsignedInteger('subject_id');
            $table->primary(['grade_id', 'subject_id']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('grade_id', 'fk_grades_subjects_id_grade')
                ->references('id')->on('grades')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('subject_id', 'fk_grades_subjects_id_subject')
                ->references('id')->on('subjects')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Lesson Periods
        Schema::create('lesson_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('weekday');
            $table->time('time_begin');
            $table->time('time_end');
            $table->unsignedInteger('semester_id');
            $table->unique(['weekday', 'time_begin', 'time_end', 'semester_id'], 'unique_period_per_semester');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('semester_id', 'fk_period_semester_id')
                ->references('id')->on('academic_semesters')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Activities
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('period_id');
            $table->unsignedInteger('class_id');
            $table->unique(['period_id', 'class_id'], 'unique_class_period');
            $table->unique(['period_id', 'teacher_id'], 'unique_teacher_period');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('subject_id', 'fk_activity_subject_id')
                ->references('id')->on('subjects')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('teacher_id', 'fk_activity_teacher_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('period_id', 'fk_activity_period_id')
                ->references('id')->on('lesson_periods')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('class_id', 'fk_activity_class_id')
                ->references('id')->on('classes')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Activity Students (Pivot)
        Schema::create('activity_students', function (Blueprint $table) {
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('activity_id');
            $table->unsignedInteger('student_order');
            $table->primary(['student_id', 'activity_id']);
            $table->unique(['activity_id', 'student_order']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('student_id', 'fk_activity_students_student_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('activity_id', 'fk_activity_students_class_id')
                ->references('id')->on('activities')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Score Distributions
        Schema::create('score_distributions', function (Blueprint $table) {
            $table->unsignedInteger('activity_id');
            $table->string('name', 255);
            $table->unsignedInteger('weight')->default(1);
            $table->primary(['activity_id', 'name']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('activity_id', 'fk_score_distribution_activity_id')
                ->references('id')->on('activities')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Activity Forms
        Schema::create('activity_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('activity_id');
            $table->date('activity_date')->default(DB::raw('(CURRENT_DATE)'));
            $table->unique(['activity_id', 'activity_date']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('activity_id', 'fk_activity_form_activity_id')
                ->references('id')->on('activities')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Activity Presences
        Schema::create('activity_presences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('form_id');
            $table->unsignedInteger('student_id');
            $table->tinyInteger('score');
            $table->string('location', 255);
            $table->unique(['form_id', 'student_id']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('form_id', 'fk_presence_form_id')
                ->references('id')->on('activity_forms')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('student_id', 'fk_presence_student_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Activity Reports
        Schema::create('activity_reports', function (Blueprint $table) {
            $table->unsignedInteger('presence_id')->primary();
            $table->tinyInteger('score');
            $table->string('topic', 255);
            $table->text('details');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('presence_id', 'fk_report_presence_id')
                ->references('id')->on('activity_presences')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Student Scores
        Schema::create('student_scores', function (Blueprint $table) {
            $table->unsignedInteger('activity_id');
            $table->unsignedInteger('student_id');
            $table->string('name', 255);
            $table->unsignedTinyInteger('score')->default(0);
            $table->primary(['activity_id', 'student_id', 'name']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('activity_id', 'fk_student_score_activity_id')
                ->references('id')->on('activities')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('student_id', 'fk_student_score_student_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('restrict');
        });
        DB::statement('ALTER TABLE student_scores ADD CONSTRAINT chk_student_scores_score CHECK (score BETWEEN 0 AND 100)');

        // Announcements
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('subtitle', 255);
            $table->text('content');
            $table->unsignedInteger('sender_id');
            $table->enum('scope', ['SPECIFIC-CLASS', 'CLASS-TAUGHT', 'SPECIFIC-GRADE', 'TEACHERS', 'PUBLIC']);
            $table->unsignedInteger('activity_id')->nullable();
            $table->unsignedInteger('grade_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sender_id', 'fk_announcement_sender_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('activity_id', 'fk_announcement_activity_id')
                ->references('id')->on('activities')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('grade_id', 'fk_announcement_grade_id')
                ->references('id')->on('grades')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // rGV: DROP VEN FROM 10KM HEIGHT
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('student_scores');
        Schema::dropIfExists('activity_reports');
        Schema::dropIfExists('activity_presences');
        Schema::dropIfExists('activity_forms');
        Schema::dropIfExists('score_distributions');
        Schema::dropIfExists('activity_students');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('lesson_periods');
        Schema::dropIfExists('academic_semesters');
        Schema::dropIfExists('grades_subjects');
        Schema::dropIfExists('majors_subjects');
        Schema::dropIfExists('users');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('majors');
    }
};