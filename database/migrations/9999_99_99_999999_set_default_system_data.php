<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->defaultRolesAndPermissions();
    }

    private function defaultRolesAndPermissions(): void
    {
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (App\Enums\Role::cases() as $role) {
            Spatie\Permission\Models\Role::findOrCreate($role->value);
        }
    }

    private function makePaymentRules(): void
    {
        $properties = App\Models\Property::first();

        foreach ($properties as $property) {
            App\Models\PaymentRule::firstOrCreate(
                ['property_id' => $property->id],
                [
                    'grace_period_days' => 3,
                    'penalty_type' => 'fixed',
                    'penalty_value' => 1000.00,
                    'auto_apply' => true,
                    'notify_tenant' => true,
                ]
            );
        }
    }
};