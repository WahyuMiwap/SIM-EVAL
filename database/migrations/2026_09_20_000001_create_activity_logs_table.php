<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jejak audit aksi sensitif: siapa — melakukan apa — ke target apa — kapan.
     * Dicatat: login/logout, kelola akun, hapus master/kegiatan,
     * ubah fase sesi, unduh rekap, ubah kustomisasi tampilan.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_aktor', 100)->nullable();
            $table->string('role_aktor', 20)->nullable();
            $table->string('aksi', 50);
            $table->string('target_type', 50)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->text('detail')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['aksi', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
