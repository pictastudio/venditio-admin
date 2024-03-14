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
    case ON_HOLD = 'on_hold';
    case PAYMENT_FAILED = 'payment_failed';
    case PAYMENT_PENDING = 'payment_pending';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

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
            self::PAYMENT_FAILED => 'Payment Failed',
            self::PAYMENT_PENDING => 'Payment Pending',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
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
            self::PAYMENT_FAILED => 'danger',
            self::PAYMENT_PENDING => 'orange',
            self::SHIPPED => 'emerald',
            self::DELIVERED => 'emerald',
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
            self::PAYMENT_FAILED => 'heroicon-o-x-mark',
            self::PAYMENT_PENDING => 'heroicon-o-pause',
            self::SHIPPED => 'heroicon-o-truck',
            self::DELIVERED => 'heroicon-o-check',
        };
    }
}
