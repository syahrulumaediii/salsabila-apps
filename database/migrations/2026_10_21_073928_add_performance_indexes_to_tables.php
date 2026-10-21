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
        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['status', 'deadline']);
            $table->index('divisi');
            $table->index('priority');
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->index('pay_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status', 'deadline']);
            $table->dropIndex(['divisi']);
            $table->dropIndex(['priority']);
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropIndex(['pay_status']);
        });
    }
};
