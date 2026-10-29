<?php

use App\StatusLogMagang;
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
        Schema::create('log_magangs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->json('jumlah_pasien_per_kategori_pelayanan_kesehatan');
            $table->text('deskripsi');
            $table->text('hambatan');
            $table->string('dokumentasi');
            $table->enum('status_verifikasi', array_column(StatusLogMagang::cases(), 'value'))->default(StatusLogMagang::Pending->value);
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->cascadeOnDelete();
            $table->foreignId('dokter_magang_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pembimbing_lapangan_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_dokter_magangs');
    }
};
