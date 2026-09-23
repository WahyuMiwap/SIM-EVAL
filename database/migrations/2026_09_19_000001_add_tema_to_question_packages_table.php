<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tema sosialisasi paket soal (mis. "P4GN Pelajar", "Ketahanan Keluarga",
     * "Lingkungan Kerja") — bebas ketik dengan saran dari tema yang sudah ada,
     * agar paket bisa dipakai ulang sekaligus adaptif per tema kegiatan.
     */
    public function up(): void
    {
        Schema::table('question_packages', function (Blueprint $table) {
            $table->string('tema', 100)->nullable()->after('nama_paket');
        });
    }

    public function down(): void
    {
        Schema::table('question_packages', function (Blueprint $table) {
            $table->dropColumn('tema');
        });
    }
};
