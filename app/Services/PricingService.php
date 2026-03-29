<?php

namespace App\Services;

use App\Models\Media;

class PricingService
{
    /**
     * Calculate the total booking price.
     *
     * Pricing types:
     *  - time  → base_price × number_of_days
     *  - unit  → base_price × quantity
     *  - cpm   → (quantity / 1000) × base_price  (quantity = impressions)
     *
     * Returns null when the media is marked as price_on_call.
     *
     * @param  Media  $media
     * @param  string $startDate   Y-m-d
     * @param  string $endDate     Y-m-d
     * @param  int    $quantity    days|units|impressions depending on pricing_type
     * @return float|null
     */
    public function calculate(Media $media, string $startDate, string $endDate, int $quantity = 1): ?float
    {
        // Price on call — no calculation, human must confirm
        if ($media->price_on_call) {
            return null;
        }

        $days = (int) \Carbon\Carbon::parse($startDate)
                        ->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;

        return match ($media->pricing_type) {
            'time'  => round($media->base_price * $days, 2),
            'unit'  => round($media->base_price * $quantity, 2),
            'cpm'   => round(($quantity / 1000) * $media->base_price, 2),
            default => round($media->base_price * $days, 2),
        };
    }

    /**
     * Explain the pricing formula used (useful for API responses).
     */
    public function explain(Media $media, string $startDate, string $endDate, int $quantity = 1): array
    {
        if ($media->price_on_call) {
            return [
                'type'    => 'price_on_call',
                'message' => 'Please contact the vendor for pricing.',
            ];
        }

        $days = (int) \Carbon\Carbon::parse($startDate)
                        ->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;

        return match ($media->pricing_type) {
            'time' => [
                'type'    => 'time',
                'formula' => "₹{$media->base_price} × {$days} days",
                'total'   => round($media->base_price * $days, 2),
            ],
            'unit' => [
                'type'    => 'unit',
                'formula' => "₹{$media->base_price} × {$quantity} units",
                'total'   => round($media->base_price * $quantity, 2),
            ],
            'cpm' => [
                'type'    => 'cpm',
                'formula' => "({$quantity} impressions ÷ 1000) × ₹{$media->base_price}",
                'total'   => round(($quantity / 1000) * $media->base_price, 2),
            ],
            default => [
                'type'  => 'unknown',
                'total' => null,
            ],
        };
    }
}
