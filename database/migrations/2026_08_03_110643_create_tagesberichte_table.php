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
        Schema::create('tagesberichte', function (Blueprint $table) {
            $table->id();
            
//            $table->string('title');
            
            $table->integer('ausbildungsnachweis_nummer');
            $table->date('datum');
            $table->string('wochentag');
            $table->string('name');
            $table->string('ausbildungsberuf');
            $table->string('betrieb');
            $table->string('backend');
            $table->integer('ausbildungsjahr');
            $table->integer('ausbildungswoche');
            
            $table->text('tätigkeiten');
            $table->text('gelernt');
            $table->text('probleme');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagesberichte');
    }
};
