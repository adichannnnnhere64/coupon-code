<?php

declare(strict_types=1);

// app/Mail/LowStockAlertMail.php

namespace App\Mail;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Coupon $coupon
    ) {}

    public function build(): self
    {
        $name = $this->coupon->operator->name ?? 'Operator';

        return $this
            ->subject('Low Stock Alert - '.$name.' Coupon')
            ->view('emails.low-stock-alert')
            ->with([
                'coupon' => $this->coupon,
            ]);
    }
}
