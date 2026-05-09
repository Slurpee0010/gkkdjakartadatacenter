<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembimbings', function (Blueprint $table) {
            // Cek jika kolom belum ada sebelum menambahkannya
            if (!Schema::hasColumn('pembimbings', 'wilayah_id')) {
                $table->unsignedBigInteger('wilayah_id');  // Kolom untuk menyimpan ID wilayah
                $table->foreign('wilayah_id')->references('id')->on('wilayahs')->onDelete('cascade');
            }

            if (!Schema::hasColumn('pembimbings', 'pelayanan_id')) {
                $table->unsignedBigInteger('pelayanan_id');  // Kolom untuk menyimpan ID pelayanan
                $table->foreign('pelayanan_id')->references('id')->on('pelayanans')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('pembimbings', function (Blueprint $table) {
            // Menghapus foreign key dan kolom jika ada
            $table->dropForeign(['wilayah_id']);
            $table->dropForeign(['pelayanan_id']);
            $table->dropColumn('wilayah_id');
            $table->dropColumn('pelayanan_id');
        });
    }
};
