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
        Schema::create('heavy_tool_costs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('heavy_tool_id');
            $table->string('area');
            $table->integer('cost');
            $table->string('unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heavy_tool_costs');
    }
};
