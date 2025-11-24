<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\InsufficientBalanceException;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function hasSufficientBalance(Money $amount): bool
    {
        return $this->balance >= $amount->getAmount();
    }

    public function deductAmount(Money $amount): void
    {
        throw_unless($this->hasSufficientBalance($amount), new InsufficientBalanceException());

        $this->balance -= $amount->getAmount();
        $this->save();
    }

    public function addAmount(Money $amount): void
    {
        $this->balance += $amount->getAmount();
        $this->save();
    }
}
