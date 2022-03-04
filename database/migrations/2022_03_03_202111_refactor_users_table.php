<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('url');
            $table->dropColumn('company_id');
            $table->dropColumn('important');
            $table->string('user_language')->default('en')->change();
            $table->renameColumn('user_language', 'locale');
            $table->dropColumn('screenshots_active');
            $table->dropColumn('avatar');
            $table->dropColumn('manual_time');
            $table->dropColumn('blur_screenshots');
            $table->dropColumn('web_and_app_monitoring');
            $table->dropColumn('computer_time_popup');
            $table->renameColumn('screenshots_interval', 'interval_duration');
            $table->dropColumn('nonce');
            $table->unsignedInteger('interval_proof_methods')->default(0);
            $table->dropColumn('change_password');
            $table->unsignedSmallInteger('type')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->string('url');
            $table->integer('company_id');
            $table->boolean('important');
            $table->string('locale')->default('')->change();
            $table->renameColumn('locale', 'user_language');
            $table->boolean('screenshots_active');
            $table->string('avatar');
            $table->boolean('manual_time');
            $table->boolean('blur_screenshots');
            $table->boolean('web_and_app_monitoring');
            $table->integer('computer_time_popup');
            $table->renameColumn('interval_duration', 'screenshots_interval');
            $table->integer('nonce')->unsigned()->default(0);
            $table->dropColumn('interval_proof_methods');
            $table->boolean('change_password')->default(false);
            $table->string('type')->default('employee')->change();
        });
    }
};
