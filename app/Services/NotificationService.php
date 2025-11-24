<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\CouponDelivery;
use App\Models\Coupon;
use App\Models\CouponTransaction;
use App\Models\Notification;
use App\Models\User;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\Mail;

final class NotificationService
{
    public function sendCouponDelivery(CouponTransaction $transaction, array $deliveryMethods): void
    {
        $user = $transaction->user;
        $coupon = $transaction->coupon;

        foreach ($deliveryMethods as $method) {
            switch ($method) {
                case 'sms':
                    $this->sendSMS();
                    break;
                case 'email':
                    $this->sendEmail($user->email, 'Coupon Delivery', $this->formatCouponEmail($coupon));
                    break;
                case 'whatsapp':
                    $this->sendWhatsApp();
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
        User::query()->find($userId);

        Notification::query()->create([
            'user_id' => $userId,
            'type' => 'wallet_credit',
            'title' => 'Wallet Credited',
            'message' => "Your wallet has been credited with {$amount->getAmount()} {$amount->getCurrency()}",
            'channels' => ['email', 'push'],
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Send actual notifications
        $this->sendPushNotification();
    }

    private function sendSMS(): void
    {
        // Integrate with SMS gateway like Twilio, MSG91, etc.
        // Implementation depends on your SMS provider
    }

    private function sendEmail(string $email, string $subject, string $message): void
    {
        // Use Laravel Mail
        Mail::to($email)->send(new CouponDelivery($subject, $message));
    }

    private function sendWhatsApp(): void
    {
        // Integrate with WhatsApp Business API
    }

    private function sendPushNotification(): void
    {
        // Integrate with FCM (Firebase Cloud Messaging)
    }

    private function formatCouponEmail(Coupon $coupon): string
    {
        return view('emails.coupon-delivery', ['coupon' => $coupon])->render();
    }
}
