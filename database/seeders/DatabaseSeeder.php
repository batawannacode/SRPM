<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Owner;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Receipt;
use App\Models\Request as MaintenanceRequest;
use App\Models\Document;
use App\Models\Expense;
use App\Models\Notification;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === OWNERS & TENANTS ===
        $owners = collect();
        $tenants = collect();

        $user = User::create([
            'first_name' => 'Owner',
            'last_name' => 'Lastname',
            'email' => "owner@example.com",
            'password' => Hash::make('password'),
        ]);

        $owner = Owner::create(['user_id' => $user->id]);
        $owners->push($owner);

        $user->assignRole('owner');

        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'first_name' => 'Tenant' . $i,
                'last_name' => 'Lastname',
                'email' => "tenant{$i}@example.com",
                'password' => Hash::make('password'),
            ]);
            $tenants->push(Tenant::create(['user_id' => $user->id]));
            $user->assignRole('tenant');
        }

        // === PROPERTIES ===
        $properties = collect();

        foreach ($owners as $index => $owner) {
            $ownerProperties = collect();

            for ($p = 1; $p <= 5; $p++) {
                $property = Property::create([
                    'owner_id' => $owner->id,
                    'name' => "Property " . (($index * 2) + $p),
                    'address' => "Blk " . rand(1, 100) . " Lot " . rand(1, 50) . ", Metro City",
                    'total_units' => rand(3, 6),
                ]);

                $ownerProperties->push($property);
                $properties->push($property);
            }

            // Set the first property created as the owner's default active property
            $owner->update([
                'active_property' => $ownerProperties->first()->id,
            ]);
        }

        // === UNITS, LEASES, PAYMENTS, PENALTIES ===
        foreach ($properties as $property) {
            $unitCount = $property->total_units;
            for ($u = 1; $u <= $unitCount; $u++) {
                $unit = Unit::create([
                    'property_id' => $property->id,
                    'unit_number' => 'Unit ' . $u,
                    'status' => ['occupied', 'maintenance', 'vacant'][rand(0, 2)],
                    'rent_type' => ['custom', 'yearly', 'monthly'][rand(0, 2)],
                    'rent_price' => rand(8000, 15000),
                ]);

                // Only some units are occupied — attach leases and tenants
                if ($unit->status === 'occupied') {
                    $tenant = $tenants->random();

                    $lease = Lease::create([
                        'unit_id' => $unit->id,
                        'tenant_id' => $tenant->id,
                        'status' => 'active',
                    ]);

                    // Payments (3 months history)
                    for ($m = 0; $m < 3; $m++) {
                        $date = Carbon::now()->subMonths($m);
                        $status = ['paid', 'pending', 'unpaid'][rand(0, 2)];
                        $payment = Payment::create([
                            'lease_id' => $lease->id,
                            'tenant_id' => $tenant->id,
                            'payment_date' => $date->format('Y-m-d'),
                            'amount' => $unit->rent_price,
                            'payment_method' => ['Bank', 'GCash', 'E-Wallet'][rand(0, 2)],
                            'account_name' => "{$tenant->user->first_name} {$tenant->user->last_name}",
                            'account_number' => str_pad(rand(100000000, 999999999), 10, '0'),
                            'reference_number' => strtoupper(Str::random(8)),
                            'proof' => 'proofs/' . Str::uuid() . '.jpg',
                            'status' => $status,
                        ]);

                        // Receipt for paid
                        if ($payment->status === 'paid') {
                            Receipt::create([
                                'payment_id' => $payment->id,
                                'receipt_number' => strtoupper(Str::random(8)),
                                'file_path' => 'receipts/' . Str::uuid() . '.pdf',
                            ]);
                        }
                    }

                    // Penalty (10% chance)
                    if (rand(1, 10) <= 2) {
                        Penalty::create([
                            'lease_id' => $lease->id,
                            'due_date' => Carbon::now()->subDays(rand(5, 15)),
                            'amount' => rand(200, 800),
                            'reason' => 'Late rent payment',
                            'is_paid' => rand(0, 1),
                        ]);
                    }

                    // Documents (lease file)
                    Document::create([
                        'lease_id' => $lease->id,
                        'tenant_id' => $tenant->id,
                        'file_name' => 'Lease_Agreement_' . $lease->id . '.pdf',
                        'file_path' => 'documents/lease_' . $lease->id . '.pdf',
                    ]);
                }
            }
        }

        // === MAINTENANCE REQUESTS ===
        foreach ($tenants as $tenant) {
            $unit = Unit::inRandomOrder()->first();
            MaintenanceRequest::create([
                'unit_id' => $unit->id,
                'tenant_id' => $tenant->id,
                'type' => rand(0, 1) ? 'maintenance' : 'complaint',
                'description' => ['Leaking faucet', 'Broken window', 'Aircon not cooling', 'Noisy neighbors'][rand(0, 3)],
                'status' => ['pending', 'in_progress', 'completed'][rand(0, 2)],
            ]);
            Expense::create(
                [
                    'property_id' => $unit->property_id,
                    'amount' => rand(100, 1000),
                    'type' => ['maintenance', 'others'][rand(0, 1)],
                ]
            );
        }

        // === NOTIFICATIONS ===
        $users = User::all();
        foreach ($users as $user) {
            for ($n = 0; $n < 2; $n++) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => ['payment_due', 'lease_expiration', 'maintenance_update'][rand(0, 2)],
                    'message' => [
                        'Your rent is due next week.',
                        'A maintenance request has been completed.',
                        'Your lease will expire soon.',
                    ][rand(0, 2)],
                    'is_read' => rand(0, 1),
                ]);
            }
        }
    }
}
