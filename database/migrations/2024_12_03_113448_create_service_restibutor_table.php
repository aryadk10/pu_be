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
        // Schema::dropIfExists('service_retributors');
        Schema::create('service_retributors', function (Blueprint $table) {
            $table->id();
            $table->integer('retributor_id');
            $table->string('upt');
            $table->string('product_text')->nullable(); // Tambahkan kolom user_id setelah id
            $table->string('product_code')->nullable(); // Tambahkan kolom user_id setelah id
            $table->integer('product_id')->nullable();
            $table->text('iplt_services')->nullable();
            $table->text('heavy_tool_services')->nullable();
            $table->text('rusunawa_services')->nullable();
            $table->text('lab_services')->nullable();
            $table->date('service_date')->nullable();
            $table->string('repeat'); // Nama depan
            $table->string('status'); // Nama depan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_retributors');
    }
};
