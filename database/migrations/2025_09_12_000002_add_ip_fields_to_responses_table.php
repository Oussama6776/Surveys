<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('responses', function (Blueprint $table) {
            if (!Schema::hasColumn('responses', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('survey_id'); // IPv6 compatible
            }
            if (!Schema::hasColumn('responses', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down()
    {
        Schema::table('responses', function (Blueprint $table) {
            if (Schema::hasColumn('responses', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            if (Schema::hasColumn('responses', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
        });
    }
};

