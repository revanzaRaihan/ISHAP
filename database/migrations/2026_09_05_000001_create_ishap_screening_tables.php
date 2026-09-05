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
        // 1. Master Gejala ISPA
        Schema::create('symptoms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Master Penyakit ISPA
        Schema::create('diseases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('severity_level')->nullable(); // ringan, sedang, berat
            $table->text('description')->nullable();
            $table->text('pathogenesis_overview')->nullable();
            $table->json('pathogenesis_causes')->nullable();
            $table->json('pathogenesis_risk_factors')->nullable();
            $table->json('recovery_tips')->nullable();
            $table->json('red_flags')->nullable();
            $table->timestamps();
        });

        // 3. Pemetaan Gejala ke Penyakit & Bobot (Symptom-Disease Map)
        Schema::create('symptom_disease_map', function (Blueprint $table) {
            $table->id();
            $table->uuid('symptom_id');
            $table->uuid('disease_id');
            $table->decimal('weight', 4, 1);
            $table->timestamps();

            $table->foreign('symptom_id')->references('id')->on('symptoms')->cascadeOnDelete();
            $table->foreign('disease_id')->references('id')->on('diseases')->cascadeOnDelete();
            $table->unique(['symptom_id', 'disease_id']);
        });

        // 4. Sesi Skrining
        Schema::create('screening_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->default('in_progress'); // in_progress, completed
            $table->timestamps();
        });

        // 5. Gejala Terpilih per Sesi
        Schema::create('session_symptoms', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_id');
            $table->uuid('symptom_id');
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('screening_sessions')->cascadeOnDelete();
            $table->foreign('symptom_id')->references('id')->on('symptoms')->cascadeOnDelete();
        });

        // 6. Hasil Skrining Mandiri (Perkiraan Risiko)
        Schema::create('screening_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_id');
            $table->uuid('disease_id');
            $table->decimal('confidence_score', 5, 1); // 0.0 - 100.0
            $table->integer('matched_symptoms_count')->default(0);
            $table->integer('total_symptoms_for_disease')->default(0);
            $table->text('reasoning');
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('screening_sessions')->cascadeOnDelete();
            $table->foreign('disease_id')->references('id')->on('diseases')->cascadeOnDelete();
        });

        // 7. Profil Dokter Mitra Konsultasi Online
        Schema::create('online_doctor_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('platform'); // Halodoc, Alodokter, dll
            $table->string('profile_url');
            $table->string('specialty');
            $table->string('hospital')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_doctor_profiles');
        Schema::dropIfExists('screening_results');
        Schema::dropIfExists('session_symptoms');
        Schema::dropIfExists('screening_sessions');
        Schema::dropIfExists('symptom_disease_map');
        Schema::dropIfExists('diseases');
        Schema::dropIfExists('symptoms');
    }
};
