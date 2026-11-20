<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_user_id');
            $table->unsignedBigInteger('salon_id');
            $table->unsignedBigInteger('employee_position_id')->nullable();
            $table->string('schedule_days')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('salon_id');
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->string('total_working_hours')->nullable();
            $table->date('attendance_date')->nullable();
            $table->string('checkin_location')->nullable();
            $table->string('checkout_location')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_pto_minutes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->integer('accrued_minutes')->default(0);
            $table->integer('used_minutes')->default(0);
            $table->integer('balance_minutes')->default(0);
            $table->timestamps();
        });

        Schema::create('pto_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('total_hours')->default(0);
            $table->text('reason')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=Pending, 1=Approved, 2=Rejected');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pto_records');
        Schema::dropIfExists('employee_pto_minutes');
        Schema::dropIfExists('employee_attendances');
        Schema::dropIfExists('employee_schedules');
    }
};