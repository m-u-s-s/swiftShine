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
        if (Schema::hasColumn('feedback', 'response_admin') && !Schema::hasColumn('feedback', 'reponse_admin')) {
            Schema::table('feedback', function (Blueprint $table) {
                $table->renameColumn('response_admin', 'reponse_admin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('feedback', 'reponse_admin') && !Schema::hasColumn('feedback', 'response_admin')) {
            Schema::table('feedback', function (Blueprint $table) {
                $table->renameColumn('reponse_admin', 'response_admin');
            });
        }
    }
};
