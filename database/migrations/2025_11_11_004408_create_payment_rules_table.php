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
        Schema::create('payment_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Property::class, 'property_id')
                ->constrained('properties')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('grace_period_days')->default(3);
            $table->enum('penalty_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('penalty_value', 8, 2)->default(1000.00);
            $table->boolean('auto_apply')->default(true);
            $table->boolean('notify_tenant')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_rules');
    }
};