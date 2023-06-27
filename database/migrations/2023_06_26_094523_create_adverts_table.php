<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->integer('view_count')->default(0);
            $table->integer('priority')->default(1);
            $table->boolean('vip_status')->default(0);
            $table->boolean('premium_status')->default(0);
            $table->boolean('market')->default(0);
            $table->integer('phone_view')->default(0);
            $table->boolean('admin_status')->default(0);
            $table->integer('owner_type')->default(1);
            $table->timestamps();
            $table->timestamp('approved_time')->nullable();
            $table->dateTime('end_time')->default(Carbon::now()->addMonth());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adverts');
    }
};
