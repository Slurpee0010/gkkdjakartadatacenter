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
        Schema::create('laporan_blesscomns', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pelaksanaan');
            $table->foreignId('id_wilayah')->constrained('wilayahs');
            $table->foreignId('id_pelayanan')->constrained('pelayanans');
            $table->foreignId('id_blesscomn')->constrained('master_blesscomns');
            $table->integer('hadir_pria')->default(0);
            $table->integer('hadir_wanita')->default(0);
            $table->integer('total_hadir')->default(0);
            $table->integer('baru_pria')->default(0);
            $table->integer('baru_wanita')->default(0);
            $table->integer('total_baru')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_blesscomns');
    }
};
