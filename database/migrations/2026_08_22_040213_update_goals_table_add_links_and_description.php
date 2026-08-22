<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            // Ubah deadline menjadi opsional (nullable)
            $table->date('deadline')->nullable()->change();

            // Tambahkan kolom baru
            $table->string('purchase_link')->nullable()->after('deadline');
            $table->text('description')->nullable()->after('purchase_link');
        });
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            // Kembalikan ke state semula
            $table->date('deadline')->nullable(false)->change();
            $table->dropColumn(['purchase_link', 'description']);
        });
    }
};
