<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'last_error')) {
                $table->text('last_error')->nullable()->after('date_envoi');
            }
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'last_error')) {
                $table->dropColumn('last_error');
            }
        });
    }
};

