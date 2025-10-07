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
        Schema::create('dentists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('crm')->unique();
            $table->string('specialization')->nullable();
            $table->integer('experience_years')->default(0);
            $table->integer('consultation_duration')->default(60); // em minutos
            $table->decimal('consultation_price', 8, 2)->default(0);
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('available_days')->nullable(); // [1,2,3,4,5] para segunda a sexta
            $table->time('available_hours_start')->default('08:00');
            $table->time('available_hours_end')->default('18:00');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dentists');
    }
};
