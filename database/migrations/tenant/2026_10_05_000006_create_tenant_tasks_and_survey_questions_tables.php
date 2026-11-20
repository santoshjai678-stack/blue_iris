<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_categorys', function (Blueprint $table) {
            $table->id();
            $table->string('task_category_name');
            $table->integer('order_by_id')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('survey_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period'); // Daily, Weekly, Monthly
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('survey_slot_times', function (Blueprint $table) {
            $table->id();
            $table->string('slot_name')->nullable();
            $table->time('slot_start_time');
            $table->time('slot_end_time');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->unsignedBigInteger('salon_id')->nullable();
            $table->unsignedBigInteger('designations_id');
            $table->unsignedBigInteger('task_categorys_id');
            $table->unsignedBigInteger('survey_periods_id');
            $table->string('task_type', 50)->default('general'); // general, expiry
            $table->string('task_tag_slug', 50)->default('general'); // cash, general, attrition
            $table->tinyInteger('control_type')->default(1)->comment('1=Radio, 2=Checkbox, 3=Text Input');
            $table->tinyInteger('is_question_mandatory')->default(0);
            $table->tinyInteger('is_other_option_add')->default(0);
            $table->date('next_renewal_date')->nullable();
            $table->decimal('next_payable_amount', 10, 2)->nullable();
            $table->integer('grace_period_day')->default(0);
            $table->text('comment')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=Active, 2=Inactive');
            $table->timestamps();
        });

        Schema::create('survey_ques_slot_timings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_questions_id');
            $table->unsignedBigInteger('survey_slot_times_id');
            $table->timestamps();
        });

        Schema::create('survey_ques_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_ques_id');
            $table->string('option');
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_expiry_salon_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_ques_id');
            $table->unsignedBigInteger('salon_id');
            $table->unsignedBigInteger('salon_items_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_expiry_salon_items');
        Schema::dropIfExists('survey_ques_options');
        Schema::dropIfExists('survey_ques_slot_timings');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('survey_slot_times');
        Schema::dropIfExists('survey_periods');
        Schema::dropIfExists('task_categorys');
    }
};