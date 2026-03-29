<?php

namespace App\Services;

use App\Models\Booking;

class AvailabilityService
{
    /**
     * Check whether a media slot is available for the requested date range.
     * Overlapping rule: existing_start <= requested_end AND existing_end >= requested_start
     *
     * @param  int    $mediaId
     * @param  string $startDate  (Y-m-d)
     * @param  string $endDate    (Y-m-d)
     * @param  int|null $excludeBookingId  — used when updating an existing booking
     * @return bool   true = available, false = conflict exists
     */
    public function isAvailable(int $mediaId, string $startDate, string $endDate, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('media_id', $mediaId)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($startDate, $endDate) {
                // Overlap condition: NOT (existing_end < new_start OR existing_start > new_end)
                $q->where('start_date', '<=', $endDate)
                  ->where('end_date',   '>=', $startDate);
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }
}
