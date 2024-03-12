<?php

namespace PictaStudio\VenditioAdmin\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';
    case ON_HOLD = 'on-hold';
    case AWAITING_PAYMENT = 'awaiting-payment';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::FAILED => 'Failed',
            self::ON_HOLD => 'On Hold',
            self::AWAITING_PAYMENT => 'Awaiting Payment',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PROCESSING => 'orange',
            self::COMPLETED => 'emerald',
            self::CANCELLED => 'danger',
            self::REFUNDED => 'sky',
            self::FAILED => 'danger',
            self::ON_HOLD => 'orange',
            self::AWAITING_PAYMENT => 'orange',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::PROCESSING => 'heroicon-o-cog',
            self::COMPLETED => 'heroicon-o-check',
            self::CANCELLED => 'heroicon-o-x-mark',
            self::REFUNDED => 'heroicon-o-receipt-refund',
            self::FAILED => 'heroicon-o-x-mark',
            self::ON_HOLD => 'heroicon-o-pause',
            self::AWAITING_PAYMENT => 'heroicon-o-banknotes',
        };
    }
}
