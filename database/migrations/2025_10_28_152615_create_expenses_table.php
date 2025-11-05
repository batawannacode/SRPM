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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Property::class, 'property_id')
                ->constrained('properties')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('amount', 65, 2);
            $table->enum('type', ['electricity', 'water', 'maintenance', 'others'])->default('others');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
