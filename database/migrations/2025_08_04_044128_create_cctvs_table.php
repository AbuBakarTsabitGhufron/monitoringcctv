<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctvs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lokasi_id')->nullable();
            $table->unsignedBigInteger('wilayah_id')->nullable();
            $table->string('nama_cctv');
            $table->string('link_stream', 500)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Foreign key
            $table->foreign('lokasi_id')->references('id')->on('lokasi')->onDelete('cascade');
            $table->foreign('wilayah_id')->references('id')->on('wilayah')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctvs');
    }
};
