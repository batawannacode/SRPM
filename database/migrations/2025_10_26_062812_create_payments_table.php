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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ExpectedPayment::class, 'expected_payment_id')
                ->constrained('expected_payments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('amount', 65, 2);
            $table->string('payment_method'); // GCash, bank, e-wallet
            $table->string('account_name');
            $table->string('account_number');
            $table->string('reference_number');
            $table->json('proof');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
