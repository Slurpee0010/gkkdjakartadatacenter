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
        Schema::create('pengurus_blesscomns', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ketua');
            $table->string('no_wa_ketua');
            $table->foreignId('id_wilayah')->constrained('wilayahs');
            $table->foreignId('id_pelayanan')->constrained('pelayanans');
            $table->string('nama_asisten');
            $table->string('no_wa_asisten');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurus_blesscomns');
    }
};
