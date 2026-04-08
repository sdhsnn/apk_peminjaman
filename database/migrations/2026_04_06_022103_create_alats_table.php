<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_alat');
            $table->string('slug')->unique();
            $table->string('kategori');
            $table->text('deskripsi')->nullable();
            $table->integer('stok_total');
            $table->integer('stok_tersedia');
            $table->enum('kondisi', ['baik', 'lecet', 'rusak', 'hilang'])->default('baik');
            $table->string('foto')->nullable();
            $table->decimal('harga_sewa', 10, 2)->default(0);
            $table->decimal('harga_asli', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alats');
    }
};
