<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retributors', function (Blueprint $table) {
            $table->id(); // Auto-increment ID
            $table->string('retributor_type'); // Jenis retributor
            $table->string('npwrd_code'); // Kode NPWRD
            $table->string('payment_code'); // Kode Bayar
            $table->string('first_name'); // Nama depan
            $table->string('last_name')->nullable(); // Nama belakang (optional)
            $table->text('address'); // Alamat
            $table->string('phone_number'); // Nomor telepon
            $table->string('email'); // Alamat surel
            $table->string('ktp_id')->nullable(); // bundling, unit, partial
            $table->string('passport_id')->nullable();
            $table->string('passport_photo')->nullable(); // Unggah foto passpor 4x6
            $table->string('ktp_photo')->nullable(); // Unggah KTP
            $table->string('family_card_photo')->nullable(); // Unggah Kartu Keluarga
            $table->string('certificate_no_home_ownership')->nullable(); // Unggah surat belum miliki rumah dari kelurahan
            $table->timestamps(); // Created at & Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retributors');
    }
};
