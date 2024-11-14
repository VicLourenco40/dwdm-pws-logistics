<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id');
            $table->unsignedBigInteger('changed_by');
            $table->foreign('changed_by')->references('id')->on('users');
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status');
    }
};
