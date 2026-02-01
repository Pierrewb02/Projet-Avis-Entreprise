<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('subject');       // Le produit ou service analysé
            $table->text('comment');         // Le texte de l'avis
            $table->integer('stars');        // La note 1 à 5
            
            // Colonnes pour l'ANALYSE AUTOMATIQUE
            $table->string('sentiment')->nullable();      // 'positive', 'negative', 'neutral'
            $table->float('sentiment_score')->nullable(); // Score de -1 à 1
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};