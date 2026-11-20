<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salon_survey_answers', function (Blueprint $table) {
            $table->id();
            $table->string('ans_unique_id', 100)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('salon_id');
            $table->unsignedBigInteger('survey_questions_id');
            $table->unsignedBigInteger('survey_periods_id');
            $table->unsignedBigInteger('slot_times_id')->nullable();
            $table->unsignedBigInteger('survey_ques_options_id')->nullable();
            $table->tinyInteger('control_type_id')->nullable();
            $table->text('ans_text')->nullable();
            $table->string('answer_location')->nullable();
            $table->date('ans_date');
            $table->timestamps();
        });

        Schema::create('mobile_otps', function (Blueprint $table) {
            $table->id();
            $table->string('phone_no', 20);
            $table->string('otp', 10);
            $table->tinyInteger('status')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('mobile_otps');
        Schema::dropIfExists('salon_survey_answers');
    }
};