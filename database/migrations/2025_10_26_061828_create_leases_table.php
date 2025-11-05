<?php

use App\Models\Tenant;
use App\Models\Unit;
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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Unit::class, 'unit_id')
                ->constrained('units')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Tenant::class, 'tenant_id')
                ->constrained('tenants')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('status', ['active', 'terminated', 'expired'])->default('active');
            $table->decimal('rent_price', 65, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
