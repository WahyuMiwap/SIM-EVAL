<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('name');
            $table->string('class_grade')->nullable(); // Kelas/kelompok
            $table->string('school_origin')->nullable(); // Asal sekolah / instansi
            $table->decimal('pretest_score', 5, 2)->nullable();
            $table->decimal('posttest_score', 5, 2)->nullable();
            $table->decimal('n_gain', 5, 2)->nullable();
            $table->string('category')->nullable(); // 'paham', 'cukup', 'kurang'
            $table->string('input_method')->default('manual'); // 'manual', 'omr', 'online'
            $table->string('status')->default('menunggu'); // 'menunggu', 'pretest', 'jeda', 'posttest', 'selesai'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
