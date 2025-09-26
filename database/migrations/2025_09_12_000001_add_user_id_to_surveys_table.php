<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            if (Schema::hasColumn('surveys', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};

