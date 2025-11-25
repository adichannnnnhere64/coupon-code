<?php

declare(strict_types=1);

// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Contracts\Services\NotificationServiceInterface;
use App\Mail\CouponDeliveryMail;
use App\Mail\LowStockAlertMail;
use App\Mail\WalletCreditMail;
use App\Models\Coupon;
use App\Models\CouponTransaction;
use App\Models\User;
use App\ValueObjects\Money;
use Exception;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Mail;

final class NotificationService implements NotificationServiceInterface
{
    public function sendCouponDelivery(CouponTransaction $transaction, array $deliveryMethods): void
    {
        $user = $transaction->user;
        $coupon = $transaction->coupon;

        foreach ($deliveryMethods as $method) {
            switch ($method) {
                case 'email':
                    $this->sendCouponEmail($transaction);
                    break;
                case 'sms':
                    $this->sendCouponSMS();
                    break;
                case 'whatsapp':
                    $this->sendCouponWhatsApp();
                    break;
                case 'print':
                    // Generate PDF for printing
                    $this->generatePrintableCoupon();
                    break;
            }
        }

        // Log notification
        Notification::query()->create([
            'user_id' => $user->id,
            'type' => 'coupon_delivery',
            'title' => 'Coupon Purchased Successfully',
            'message' => "Your {$coupon->denomination} coupon has been delivered",
            'channels' => $deliveryMethods,
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function sendWalletCreditNotification(int $userId, Money $amount): void
    {
        $user = User::query()->find($userId);
        $wallet = $user->wallet;

        // Send email notification
        $this->sendWalletCreditEmail($user, $amount, $wallet->balance);

        // Send other notifications if needed
        $this->sendPushNotification();

        // Log notification
        Notification::query()->create([
            'user_id' => $userId,
            'type' => 'wallet_credit',
            'title' => 'Wallet Credited',
            'message' => "Your wallet has been credited with {$amount->getAmount()} {$amount->getCurrency()}",
            'channels' => ['email', 'push'],
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function sendLowStockAlert(Coupon $coupon): void
    {
        // Get admin users to notify
        $adminUsers = User::query()->where('email', 'like', '%admin%')
            ->orWhere('email', 'like', '%@admin.%')
            ->get();

        foreach ($adminUsers as $admin) {
            $this->sendLowStockEmail($admin, $coupon);
        }

        // Log notification
        Notification::query()->create([
            'user_id' => null, // Broadcast to all admins
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => "Coupon {$coupon->coupon_code} is running low on stock",
            'channels' => ['email'],
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    private function sendCouponEmail(CouponTransaction $transaction): void
    {
        try {
            Mail::to($transaction->user->email)
                ->send(new CouponDeliveryMail($transaction));
        } catch (Exception $e) {
            FacadesLog::error('Failed to send coupon email: '.$e->getMessage());
        }
    }

    private function sendWalletCreditEmail(User $user, Money $amount, float|string $newBalance): void
    {
        try {
            Mail::to($user->email)
                ->send(new WalletCreditMail(
                    $amount->getAmount(),
                    $amount->getCurrency(),
                    $newBalance
                ));
        } catch (Exception $e) {
            FacadesLog::error('Failed to send wallet credit email: '.$e->getMessage());
        }
    }

    private function sendLowStockEmail(User $admin, Coupon $coupon): void
    {
        try {
            Mail::to($admin->email)
                ->send(new LowStockAlertMail($coupon));
        } catch (Exception $e) {
            FacadesLog::error('Failed to send low stock alert email: '.$e->getMessage());
        }
    }

    // Keep your existing SMS, WhatsApp, and other methods...
    private function sendCouponSMS(): void
    {
        // SMS implementation
    }

    private function sendCouponWhatsApp(): void
    {
        // WhatsApp implementation
    }

    private function generatePrintableCoupon(): void
    {
        // PDF generation implementation
    }

    private function sendPushNotification(): void
    {
        // Push notification implementation
    }
}
