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
        Schema::create('laporan_pas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained('wilayahs');
            $table->foreignId('pelayanan_id')->constrained('pelayanans');
            $table->foreignId('pembimbing_id')->constrained('pembimbings');
            $table->foreignId('anak_pa_id')->constrained('anak_bimbingans');
            $table->foreignId('buku_pa_id')->nullable()->constrained('master_buku_pas');
            $table->string('buku_pa_lainnya')->nullable();
            $table->integer('bab');
            $table->date('tanggal_pa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pas');
    }
};
