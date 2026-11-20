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
        Schema::table('salons', function (Blueprint $table) {
            if (!Schema::hasColumn('salons', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('city');
            }
            if (!Schema::hasColumn('salons', 'state_id')) {
                $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn(['country_id', 'state_id']);
        });
    }
};