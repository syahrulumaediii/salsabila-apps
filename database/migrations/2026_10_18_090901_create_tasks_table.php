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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->string('stage');
            $table->string('sub_stage')->nullable();
            $table->string('divisi')->nullable();
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('support_team')->nullable();
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->string('priority')->default('Sedang');
            $table->string('status')->default('Belum Dimulai');
            $table->unsignedTinyInteger('weight')->default(10);
            $table->boolean('manual_progress_mode')->default(false);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('obstacle')->nullable();
            $table->text('notes')->nullable();
            $table->text('next_action')->nullable();
            $table->boolean('need_decision')->default(false);
            $table->boolean('client_follow_up')->default(false);
            $table->date('last_updated_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
