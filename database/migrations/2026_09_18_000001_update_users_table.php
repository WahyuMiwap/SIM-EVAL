<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operator')->after('password'); // 'superadmin', 'operator', 'magang'
            $table->string('nip', 30)->nullable()->after('role');
            $table->string('jabatan')->nullable()->after('nip');
            $table->string('avatar')->nullable()->after('jabatan');
            $table->boolean('is_active')->default(true)->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nip', 'jabatan', 'avatar', 'is_active']);
        });
    }
};
