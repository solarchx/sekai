<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('score_distribution_weights');
        Schema::dropIfExists('score_distributions');
        Schema::dropIfExists('student_score_details');
        Schema::dropIfExists('student_scores');

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('score_distributions');
        Schema::dropIfExists('student_scores');

        // Score Distributions
        Schema::create('score_distributions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('activity_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('activity_id', 'fk_score_distribution_activity_id')
                ->references('id')->on('activities')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Score Distribution Weights
        Schema::create('score_distribution_weights', function (Blueprint $table) {
            $table->unsignedInteger('distribution_id')->primary();
            $table->string('name', 255);
            $table->integer('weight');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('distribution_id', 'fk_weight_score_distribution_id')
                ->references('id')->on('score_distributions')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Student Scores
        Schema::create('student_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('activity_id');
            $table->unsignedInteger('student_id');
            $table->unique(['activity_id', 'student_id']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('activity_id', 'fk_student_score_activity_id')
                ->references('id')->on('activities')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('student_id', 'fk_student_score_student_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Student Score Details
        Schema::create('student_score_details', function (Blueprint $table) {
            $table->unsignedInteger('score_id')->primary();
            $table->string('name', 255);
            $table->tinyInteger('score');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('score_id', 'fk_student_score_id')
                ->references('id')->on('student_scores')
                ->onUpdate('cascade')->onDelete('restrict');
        });
        DB::statement('ALTER TABLE student_score_details ADD CONSTRAINT chk_student_score_details_score CHECK (score BETWEEN 0 AND 100)');
    }
};
