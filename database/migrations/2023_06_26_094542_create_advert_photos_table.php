<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advert_photos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('advert_photos');
    }
};
