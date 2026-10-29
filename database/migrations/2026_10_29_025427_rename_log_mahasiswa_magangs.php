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
        $oldTable = 'log_mahasiswa_magang';
        $newTable = 'log_mahasiswa_magangs';

        if (Schema::hasTable($oldTable) && !Schema::hasTable($newTable)) {
            Schema::rename($oldTable, $newTable);
        }

        elseif (!Schema::hasTable($newTable)) {
            Schema::create($newTable, function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->text('nama_kegiatan');
                $table->text('deskripsi');
                $table->enum('status_verifikasi', array_column(StatusLogMagang::cases(), 'value'))->default(StatusLogMagang::Pending->value);
                $table->foreignId('mahasiswa_magang_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('pembimbing_lapangan_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }
        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $oldTable = 'log_mahasiswa_magang';
        $newTable = 'log_mahasiswa_magangs';

        if (Schema::hasTable($newTable) && !Schema::hasTable($oldTable)) {
            Schema::rename($newTable, $oldTable);
        }
    }
};
