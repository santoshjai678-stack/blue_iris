<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->default('');
            $table->string('email')->unique();
            $table->string('phone_no', 20)->nullable();
            $table->string('password');
            $table->unsignedBigInteger('roles_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('mobile_no_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};