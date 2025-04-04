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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Le nom de la tâche
            $table->text('description');  // Description de la tâche
            $table->string('guideUrl')->nullable();  // URL du guide (si disponible)
            $table->string('registrationUrl')->nullable();  // URL d'enregistrement (si disponible)
            $table->json('pieces');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
