<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update events table with v2.0 operational fields
        Schema::table('events', function (Blueprint $table) {
            $table->string('kode_event', 40)->nullable()->after('id');
            $table->string('kategori_audiens', 30)->default('SMA')->after('catatan'); // SD, SMP, SMA, LAPAS, UMUM, INSTANSI
            $table->string('mode_input', 20)->default('DIGITAL_PWA')->after('kategori_audiens'); // MANUAL_KERTAS, DIGITAL_PWA, HYBRID
            $table->string('digital_submode', 20)->default('TERBUKA')->after('mode_input'); // TERBUKA, TERDAFTAR
            $table->boolean('enable_custody_tracking')->default(false)->after('digital_submode');
            $table->string('status_fase', 20)->default('DRAFT')->after('enable_custody_tracking'); // DRAFT, PRE_ACTIVE, MATERIAL_PAUSED, POST_ACTIVE, COMPLETED
        });

        // 2. Update participants table with v2.0 fields
        Schema::table('participants', function (Blueprint $table) {
            $table->integer('nomor_absen')->nullable()->after('event_id');
            $table->string('nama_normalized', 150)->nullable()->after('name');
            $table->string('keterangan', 100)->nullable()->after('school_origin');
            $table->decimal('delta', 5, 2)->nullable()->after('posttest_score');
            $table->string('status_data', 20)->default('INCOMPLETE_RECORD')->after('category'); // COMPLETE, INCOMPLETE_RECORD
            $table->string('serial_pre', 30)->nullable()->after('status_data');
            $table->string('serial_post', 30)->nullable()->after('serial_pre');
            $table->string('session_token', 64)->nullable()->after('serial_post');
        });

        // 3. Update question_packages table with v2.0 fields
        Schema::table('question_packages', function (Blueprint $table) {
            $table->string('kategori_audiens', 50)->default('UMUM')->after('nama_paket');
            $table->integer('jumlah_opsi')->default(4)->after('kategori_audiens');
        });

        // 4. Update users table with v2.0 fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('bidang_wilayah', 100)->nullable()->after('jabatan');
        });

        // 5. Create scans table (OMR / physical scan log & digital audit)
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('participant_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->string('phase_type', 10); // PRE, POST
            $table->json('raw_answers');
            $table->integer('score_raw')->default(0);
            $table->decimal('score_percent', 5, 2)->default(0);
            $table->text('photo_path')->nullable();
            $table->string('omr_confidence', 10)->default('HIGH'); // HIGH, LOW
            $table->foreignId('captured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('captured_at')->nullable();
            $table->string('capture_method', 20)->default('DIGITAL_PWA'); // CAMERA_LIVE, PDF_BATCH_ADF, DIGITAL_PWA
            $table->string('serial_number', 30)->nullable();
            $table->timestamps();
        });

        // 6. Create reconciliation_queue table (Layar Pengecualian)
        Schema::create('reconciliation_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('scan_id')->nullable()->constrained('scans')->nullOnDelete();
            $table->string('issue_type', 30); // DUPLICATE_NAME, NO_MATCH_POST, MISSING_SIDE, LOW_CONFIDENCE_OMR
            $table->json('detail')->nullable();
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // 7. Create custody_log table (Khusus Lapas)
        Schema::create('custody_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('handed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('handed_to', 100)->nullable();
            $table->string('location_note', 150)->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custody_log');
        Schema::dropIfExists('reconciliation_queue');
        Schema::dropIfExists('scans');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bidang_wilayah']);
        });

        Schema::table('question_packages', function (Blueprint $table) {
            $table->dropColumn(['kategori_audiens', 'jumlah_opsi']);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_absen',
                'nama_normalized',
                'keterangan',
                'delta',
                'status_data',
                'serial_pre',
                'serial_post',
                'session_token',
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'kode_event',
                'kategori_audiens',
                'mode_input',
                'digital_submode',
                'enable_custody_tracking',
                'status_fase',
            ]);
        });
    }
};
