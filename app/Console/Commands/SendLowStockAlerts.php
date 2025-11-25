<?php

declare(strict_types=1);

// app/Console/Commands/SendLowStockAlerts.php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Services\NotificationService;
use Illuminate\Console\Command;

final class SendLowStockAlerts extends Command
{
    protected $signature = 'alerts:low-stock';

    protected $description = 'Send low stock alerts for coupons';

    public function handle(NotificationService $notificationService): void
    {
        $lowStockCoupons = Coupon::query()->whereRaw('stock_quantity <= low_stock_threshold')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();

        foreach ($lowStockCoupons as $coupon) {
            $notificationService->sendLowStockAlert($coupon);
            $this->info("Sent low stock alert for {$coupon->operator->name} - {$coupon->denomination}");
        }

        $this->info("Sent {$lowStockCoupons->count()} low stock alerts.");
    }
}
