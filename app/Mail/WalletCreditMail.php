<?php

declare(strict_types=1);

// app/Mail/WalletCreditMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class WalletCreditMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public float|string $amount,
        public string $currency,
        public float|string $newBalance
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Wallet Credited - '.config('app.name'))
            ->view('emails.wallet-credit')
            ->with([
                'amount' => $this->amount,
                'currency' => $this->currency,
                'newBalance' => $this->newBalance,
            ]);
    }
}
