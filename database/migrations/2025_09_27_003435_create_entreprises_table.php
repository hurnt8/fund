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
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id(); // La clé primaire auto-incrémentée (ID)

            // Nom de l'entreprise (obligatoire)
            $table->string('nom_entreprise', 255)->unique();

            // Adresse de résidence/siège social (séparée pour une meilleure gestion)
            $table->string('adresse_ligne1', 255);
            $table->string('adresse_ligne2', 255)->nullable(); // Ligne optionnelle
            $table->string('ville', 100);
            $table->string('code_postal', 20)->nullable();
            $table->string('pays', 100);

            // Coordonnées de contact (le téléphone est obligatoire)
            $table->string('telephone_principal', 50);
            $table->string('email_contact', 255)->nullable();

            // Horodatages créés_at et updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};