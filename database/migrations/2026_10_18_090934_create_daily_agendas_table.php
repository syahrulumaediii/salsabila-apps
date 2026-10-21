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
        Schema::create('daily_agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('agenda_date');
            $table->json('top_task_ids')->nullable();
            $table->text('eval_q1')->nullable();
            $table->text('eval_q2')->nullable();
            $table->text('eval_q3')->nullable();
            $table->string('eval_q4')->nullable();
            $table->text('eval_q5')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'agenda_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_agendas');
    }
};
