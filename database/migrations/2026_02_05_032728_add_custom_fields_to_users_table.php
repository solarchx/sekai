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
        Schema::table('users', function (Blueprint $table) {
            $table->string('identifier', 31)->unique()->after('email');
            $table->enum('role', ['STUDENT', 'TEACHER', 'VP', 'ADMIN'])->after('identifier');
            $table->boolean('deleted')->default(0)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['identifier', 'role', 'deleted']);
        });
    }
};
