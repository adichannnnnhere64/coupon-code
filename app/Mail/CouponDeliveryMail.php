<?php

declare(strict_types=1);

// app/Mail/CouponDeliveryMail.php

namespace App\Mail;

use App\Models\CouponTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class CouponDeliveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CouponTransaction $transaction
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Your Mobile Recharge Coupon - '.config('app.name'))
            ->view('emails.coupon-delivery')
            ->with([
                'transaction' => $this->transaction,
                'coupon' => $this->transaction->coupon,
                'user' => $this->transaction->user,
            ]);
    }
}
