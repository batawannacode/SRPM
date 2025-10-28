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
};
