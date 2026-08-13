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
        Schema::table('users', function (Blueprint $table) {
            $table->string('vorname')->nullable()->after('name');
            $table->string('nachname')->nullable()->after('vorname');
            $table->string('ausbildungsberuf')->nullable();
            $table->string('ausbildungsbetrieb')->nullable();
            $table->date('ausbildungsbeginn')->nullable();
            $table->timestamp('ausbildung_info_completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
