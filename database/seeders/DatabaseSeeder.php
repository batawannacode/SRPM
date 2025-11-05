<?php

namespace Database\Seeders;

use App\Models\{Lease, Payment, ExpectedPayment, Receipt, Penalty, Document, Unit, Expense, Notification, Property, Tenant, Owner, User, Request as MaintenanceRequest};
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'phone_number' => '09171234567',
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
                'phone_number' => '0917' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
            ]);
            $tenants->push(Tenant::create(['user_id' => $user->id]));
            $user->assignRole('tenant');
        }

        // === PROPERTIES ===
        $properties = collect();

        foreach ($owners as $index => $owner) {
            $ownerProperties = collect();

            for ($p = 1; $p <= 10; $p++) {
                $property = Property::create([
                    'owner_id' => $owner->id,
                    'name' => "Property " . (($index * 2) + $p),
                    'address' => "Blk " . rand(1, 100) . " Lot " . rand(1, 50) . ", Metro City",
                    'total_units' => 100,
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
                ]);

                Expense::create([
                    'property_id' => $property->id,
                    'amount' => rand(8000, 10000),
                    'type' => ['electricity', 'water', 'maintenance', 'others'][rand(0, 3)],
                ]);

                if ($unit->status === 'occupied') {
                    $tenant = $tenants->random();

                    // === LEASE ===
                    $startDate = Carbon::now()->subMonths(rand(1, 12))->startOfMonth();
                    $endDate   = (clone $startDate)->addMonths(rand(6, 18))->endOfMonth();

                    $lease = Lease::create([
                        'unit_id' => $unit->id,
                        'tenant_id' => $tenant->id,
                        'status' => 'active',
                        'rent_price' => rand(8000, 15000),
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);

                    // === EXPECTED PAYMENTS & PAYMENTS ===
                    $months = $startDate->diffInMonths($endDate);
                    $current = $startDate->copy();

                    for ($i = 0; $i < $months; $i++) {
                        $expectedDate = $current->copy()->endOfMonth();

                        // Create expected payment record
                        $expectedPayment = ExpectedPayment::create([
                            'lease_id' => $lease->id,
                            'payment_date' => $expectedDate,
                            'status' => 'unpaid',
                        ]);

                        // Randomly decide if tenant paid for this month
                        $isPaid = rand(0, 1);

                        if ($isPaid) {
                            $payment = Payment::create([
                                'expected_payment_id' => $expectedPayment->id,
                                'amount' => $lease->rent_price,
                                'payment_method' => ['Bank', 'GCash', 'E-Wallet'][rand(0, 2)],
                                'account_name' => "{$tenant->user->first_name} {$tenant->user->last_name}",
                                'account_number' => str_pad(rand(100000000, 999999999), 10, '0'),
                                'reference_number' => strtoupper(Str::random(8)),
                                'proof' => 'proofs/' . Str::uuid() . '.jpg',
                            ]);

                            // Update expected payment status to paid
                            $expectedPayment->update(['status' => 'paid']);

                            // Optionally create receipt
                            Receipt::create([
                                'payment_id' => $payment->id,
                                'receipt_number' => strtoupper(Str::random(8)),
                                'file_path' => 'receipts/' . Str::uuid() . '.pdf',
                            ]);
                        }

                        $current->addMonth();
                    }

                    // === PENALTIES (optional) ===
                    if (rand(1, 10) <= 5) {
                        Penalty::create([
                            'lease_id' => $lease->id,
                            'due_date' => Carbon::now()->subDays(rand(5, 15)),
                            'amount' => rand(200, 800),
                            'reason' => 'Late rent payment',
                            'is_paid' => rand(0, 1),
                        ]);
                    }

                    // === DOCUMENT ===
                    $sourceFiles = [
                        'documents/SAMPLE LEASE.pdf',
                        'documents/SAMPLE LEASE.docx',
                        'documents/sample.png',
                    ];

                    // Pick one randomly
                    $sourceFile = $sourceFiles[array_rand($sourceFiles)];

                    // Define the lease-specific folder and filename
                    $folder = 'documents/lease_' . $lease->id;
                    $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);
                    $destinationFile = $folder . '/Lease_Agreement_' . $lease->id . '.' . $extension;

                    // Ensure the lease folder exists
                    Storage::disk('public')->makeDirectory($folder);

                    // Copy the sample file into the lease-specific folder
                    if (Storage::disk('public')->exists($sourceFile)) {
                        Storage::disk('public')->copy($sourceFile, $destinationFile);
                    } else {
                        throw new \Exception("Source sample file not found: {$sourceFile}");
                    }

                    // Create the document record
                    Document::create([
                        'lease_id' => $lease->id,
                        'file_name' => basename($destinationFile),
                        'file_path' => $destinationFile,
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
            for ($n = 0; $n < 20; $n++) {
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
