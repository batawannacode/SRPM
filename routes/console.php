<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule the command to run every hour.
 *
 * CheckLeasePayments command checks all leases for near or overdue payments
 * and applies penalties if needed.
 */
Schedule::command('app:check-lease-payments')->hourly();

/**
 * Schedule the command to run every hour.
 *
 * CheckLeaseExpirations command notifies tenants and owners when leases are
 * near expiration.
 */
Schedule::command('app:check-lease-expirations')->hourly();
