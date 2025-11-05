<?php

use App\Models\Lease;
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
        Schema::create('expected_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Lease::class, 'lease_id')
                ->constrained('leases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('payment_date');
            $table->enum('status', ['paid', 'unpaid', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expected_payments');
    }
};
