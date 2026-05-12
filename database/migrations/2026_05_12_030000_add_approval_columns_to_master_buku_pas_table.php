<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_buku_pas', function (Blueprint $table) {
            $table->string('status')->default('approved')->after('jumlah_bab');
            $table->foreignId('requested_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->after('requested_by')->constrained('users')->nullOnDelete();
            $table->timestamp('requested_at')->nullable()->after('reviewed_by');
            $table->timestamp('reviewed_at')->nullable()->after('requested_at');
            $table->text('review_note')->nullable()->after('reviewed_at');
            $table->index(['status', 'created_at']);
        });

        DB::table('master_buku_pas')
            ->whereNull('requested_at')
            ->update([
                'status' => 'approved',
                'reviewed_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('master_buku_pas', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'status',
                'requested_by',
                'reviewed_by',
                'requested_at',
                'reviewed_at',
                'review_note',
            ]);
        });
    }
};
