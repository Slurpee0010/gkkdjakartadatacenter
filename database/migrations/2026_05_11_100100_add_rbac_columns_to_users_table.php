<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained()->nullOnDelete();
            $table->foreignId('wilayah_id')->nullable()->after('role_id')->constrained('wilayahs')->nullOnDelete();
            $table->string('status')->default('active')->after('wilayah_id');
            $table->foreignId('deletion_requested_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('deletion_requested_at')->nullable()->after('deletion_requested_by');
            $table->softDeletes();

            $table->index(['role_id', 'status']);
            $table->index(['wilayah_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['deletion_requested_by']);
            $table->dropForeign(['wilayah_id']);
            $table->dropForeign(['role_id']);
            $table->dropIndex(['role_id', 'status']);
            $table->dropIndex(['wilayah_id', 'status']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'role_id',
                'wilayah_id',
                'status',
                'deletion_requested_by',
                'deletion_requested_at',
            ]);
        });
    }
};
