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
        Schema::create('anak_bimbingans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_anak');
            $table->foreignId('pembimbing_id')->constrained('pembimbings');
            $table->foreignId('wilayah_id')->constrained('wilayahs');
            $table->foreignId('pelayanan_id')->constrained('pelayanans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anak_bimbingans');
    }
};
