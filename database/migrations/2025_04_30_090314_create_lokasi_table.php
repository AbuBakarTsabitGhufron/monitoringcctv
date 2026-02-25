<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLokasiTable extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->string('alamat')->nullable();
            $table->unsignedBigInteger('wilayah_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('wilayah_id')->references('id')->on('wilayah')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi');
    }
}
