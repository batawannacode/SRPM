<?php

use App\Models\Lease;
use App\Models\Tenant;
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
            $table->foreignIdFor(Lease::class, 'lease_id')
                ->constrained('leases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Tenant::class, 'tenant_id')
                ->constrained('tenants')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // GCash, bank, e-wallet
            $table->string('account_name');
            $table->string('account_number');
            $table->string('reference_number');
            $table->string('proof');
            $table->enum('status', ['paid', 'unpaid', 'pending'])->default('pending');
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
