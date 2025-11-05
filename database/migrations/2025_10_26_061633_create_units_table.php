<?php

use App\Models\Property;
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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Property::class, 'property_id')
                ->constrained('properties')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('unit_number');
            $table->enum('status', ['vacant', 'occupied', 'maintenance'])->default('vacant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};