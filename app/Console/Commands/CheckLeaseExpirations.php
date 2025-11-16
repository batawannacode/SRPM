<?php

namespace App\Console\Commands;

use App\Models\Lease;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckLeaseExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-lease-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify tenants and owners when leases are near expiration.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now()->startOfDay();

        // Fetch all active leases with relationships
        $leases = Lease::with(['tenant.user', 'unit.property.owner'])->get();

        foreach ($leases as $lease) {
            if (! $lease->end_date) continue;

            $endDate = Carbon::parse($lease->end_date)->startOfDay();
            $daysUntilEnd = $now->diffInDays($endDate, false);

            // Notify when lease is near to expire (within 30 days)
            if ($daysUntilEnd <= 30 && $daysUntilEnd >= 0) {
                $tenantUser = $lease->tenant?->user;
                $ownerUserId = $lease->unit->property->owner->user_id ?? null;

                if ($tenantUser) {
                    Notification::firstOrCreate([
                        'user_id' => $tenantUser->id,
                        'type' => 'lease_expiration',
                        'message' => "Your lease will expire on {$endDate->format('F j, Y')}. Please contact the property owner if you wish to renew.",
                    ]);
                }

                if ($ownerUserId) {
                    Notification::firstOrCreate([
                        'user_id' => $ownerUserId,
                        'type' => 'lease_expiration',
                        'message' => "Tenant {$tenantUser->full_name} has a lease that will expire on {$endDate->format('F j, Y')}.",
                    ]);
                }
            }

            // Notify when lease already expired
            if ($daysUntilEnd < 0) {
                $tenantUser = $lease->tenant?->user;
                $ownerUserId = $lease->unit->property->owner->user_id ?? null;

                if ($tenantUser) {
                    Notification::firstOrCreate([
                        'user_id' => $tenantUser->id,
                        'type' => 'lease_expiration',
                        'message' => "Your lease expired on {$endDate->format('F j, Y')}. Please settle any remaining obligations or contact the owner.",
                    ]);
                }

                if ($ownerUserId) {
                    Notification::firstOrCreate([
                        'user_id' => $ownerUserId,
                        'type' => 'lease_expiration',
                        'message' => "Tenant {$tenantUser->full_name}'s lease expired on {$endDate->format('F j, Y')}.",
                    ]);
                }
            }
        }

        $this->info('✅ Lease expiration notifications sent successfully at ' . $now);
    }
}
