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
        Schema::create('kehadiran_ibadah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('id_wilayah')->constrained('wilayahs')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_pelayanan')->constrained('pelayanans')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_ibadah');
            $table->boolean('is_nama_manual')->default(false);
            $table->date('tanggal_ibadah');
            $table->unsignedInteger('hadir_pria_onsite')->default(0);
            $table->unsignedInteger('hadir_wanita_onsite')->default(0);
            $table->unsignedInteger('total_hadir_onsite')->default(0);
            $table->unsignedInteger('hadir_pria_online')->default(0);
            $table->unsignedInteger('hadir_wanita_online')->default(0);
            $table->unsignedInteger('total_hadir_online')->default(0);
            $table->unsignedInteger('baru_pria')->default(0);
            $table->unsignedInteger('baru_wanita')->default(0);
            $table->unsignedInteger('total_baru')->default(0);
            $table->unsignedInteger('grand_total')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tanggal_ibadah', 'id_wilayah', 'id_pelayanan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran_ibadah');
    }
};
