<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name', 10)->nullable();
            $table->string('phone_code', 10)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id')->default(1);
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('salons', function (Blueprint $table) {
            $table->id();
            $table->string('salon_name');
            $table->string('salon_email')->nullable();
            $table->string('salon_phone', 20)->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->decimal('salon_lat', 10, 7)->nullable();
            $table->decimal('salon_long', 10, 7)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps();
        });

        Schema::create('salon_timings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salon_id');
            $table->string('day_name', 20);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->tinyInteger('is_closed')->default(0);
            $table->timestamps();
        });

        Schema::create('salon_items', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_items');
        Schema::dropIfExists('salon_timings');
        Schema::dropIfExists('salons');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
};