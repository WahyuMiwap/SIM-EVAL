<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->string('kode_join', 10)->unique();
            $table->string('status')->default('dijadwalkan'); // 'dijadwalkan', 'berlangsung', 'selesai'
            $table->date('tanggal')->nullable();
            $table->integer('durasi_menit')->default(30);
            $table->text('catatan')->nullable();
            $table->foreignId('lokasi_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('pretest_package_id')->nullable()->constrained('question_packages')->nullOnDelete();
            $table->foreignId('posttest_package_id')->nullable()->constrained('question_packages')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
