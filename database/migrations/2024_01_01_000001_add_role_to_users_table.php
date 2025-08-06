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
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('email');
            $table->string('student_id')->nullable()->unique()->after('role');
            $table->string('phone')->nullable()->after('student_id');
            $table->text('address')->nullable()->after('phone');
            
            $table->index('role');
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['student_id']);
            $table->dropColumn(['role', 'student_id', 'phone', 'address']);
        });
    }
};