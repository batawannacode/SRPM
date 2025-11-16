<?php

use App\Models\ExpectedPayment;
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
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ExpectedPayment::class, 'expected_payment_id')
                ->constrained('expected_payments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('due_date');
            $table->decimal('amount', 65, 2);
            $table->string('reason')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};