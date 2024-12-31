<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique()->nullable();
            $table->string('qris_link')->nullable();
            $table->string('npwrd');
            $table->unsignedInteger('service_id')->nullable();
            $table->string('status');
            $table->integer('amount');
            $table->integer('total');
            $table->dateTime('payment_expired')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->date('invoice_date')->nullable();
            $table->timestamps();
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('bjb_client_type')->nullable();
            $table->string('bjb_product_code')->nullable();
            $table->string('bjb_invoice_no')->nullable();
            $table->string('bjb_description')->nullable();
            $table->string('bjb_customer_name')->nullable();
            $table->string('bjb_customer_email')->nullable();
            $table->string('bjb_customer_phone')->nullable();
            $table->dateTime('bjb_expired_date')->nullable();
            $table->decimal('bjb_amount', 15, 2)->nullable();
            $table->text('bjb_qrcode')->nullable();
            $table->string('type')->nullable();
            $table->string('upt')->nullable();
            $table->longText('details')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
