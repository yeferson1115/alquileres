<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('credit_applications', function (Blueprint $table) {
            $table->string('reference_contact_1_name')->nullable()->after('phone_secondary');
            $table->string('reference_contact_1_phone', 30)->nullable()->after('reference_contact_1_name');
            $table->string('reference_contact_2_name')->nullable()->after('reference_contact_1_phone');
            $table->string('reference_contact_2_phone', 30)->nullable()->after('reference_contact_2_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_applications', function (Blueprint $table) {
            $table->dropColumn([
                'reference_contact_1_name',
                'reference_contact_1_phone',
                'reference_contact_2_name',
                'reference_contact_2_phone',
            ]);
        });
    }
};
