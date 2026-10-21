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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('client');
            $table->decimal('contract_val', 15, 2)->default(0);
            $table->string('term');
            $table->decimal('invoice_val', 15, 2)->default(0);
            $table->decimal('paid_val', 15, 2)->default(0);
            $table->date('submit_date')->nullable();
            $table->string('doc_status')->default('Penyusunan BAST & Berkas');
            $table->string('pay_status')->default('Belum Diajukan');
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
