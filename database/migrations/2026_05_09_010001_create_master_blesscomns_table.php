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
        Schema::create('master_blesscomns', function (Blueprint $table) {
            $table->id();
            $table->string('nama_blesscomn');
            $table->date('tanggal_terbentuk');
            $table->foreignId('id_pengurus')->constrained('pengurus_blesscomns');
            $table->foreignId('id_wilayah')->constrained('wilayahs');
            $table->foreignId('id_pelayanan')->constrained('pelayanans');
            $table->boolean('is_pembelahan')->default(false);
            $table->foreignId('id_blesscomn_induk')->nullable()->constrained('master_blesscomns');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_blesscomns');
    }
};
