<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->tinyInteger('is_public')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_position', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_no', 20)->nullable();
            $table->date('dob')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('rejoin_date')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_designation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_user_id');
            $table->unsignedBigInteger('designation_id');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_hire_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_hire_records');
        Schema::dropIfExists('employee_designation');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('employee_position');
        Schema::dropIfExists('designations');
    }
};