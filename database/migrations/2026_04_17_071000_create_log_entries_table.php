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
        Schema::create('log_entries', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->string('vendor_name');
            $table->string('purpose');
            $table->integer('quantity')->default(1);
            $table->text('description');
            $table->dateTime('timestamp_in');
            $table->dateTime('timestamp_out')->nullable();
            $table->string('status')->default('INSIDE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_entries');
    }
};
