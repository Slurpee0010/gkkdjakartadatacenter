<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ticket 4: Add soft deletes to master_buku_pas and laporan_pas tables.
     */
    public function up(): void
    {
        Schema::table('master_buku_pas', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('laporan_pas', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_buku_pas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('laporan_pas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
