<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Media;
use Illuminate\Support\Str;

class BookingService
{
    protected $availabilityService;
    protected $pricingService;

    public function __construct(
        AvailabilityService $availabilityService,
        PricingService $pricingService
    ) {
        $this->availabilityService = $availabilityService;
        $this->pricingService      = $pricingService;
    }

    /**
     * Create a new booking.
     *
     * @throws \Exception
     */
    public function createBooking(int $userId, array $data): Booking
    {
        $media = Media::find($data['media_id']);

        if (!$media) {
            throw new \Exception('Media not found.');
        }

        if ($media->status !== 'active') {
            throw new \Exception('This media is not available for booking.');
        }

        // Validate dates
        $startDate = $data['start_date'];
        $endDate   = $data['end_date'];

        if ($startDate > $endDate) {
            throw new \Exception('start_date must be before end_date.');
        }

        // Check availability
        $available = $this->availabilityService->isAvailable(
            $media->id,
            $startDate,
            $endDate
        );

        if (!$available) {
            throw new \Exception('This media is already booked for the selected dates.');
        }

        // Calculate price
        $quantity   = $data['quantity'] ?? 1;
        $totalPrice = $this->pricingService->calculate($media, $startDate, $endDate, $quantity);

        return Booking::create([
            'user_id'           => $userId,
            'media_id'          => $media->id,
            'booking_reference' => 'BK-' . strtoupper(Str::random(8)),
            'start_date'        => $startDate,
            'end_date'          => $endDate,
            'total_price'       => $totalPrice,         // null if price_on_call
            'price_on_call'     => $media->price_on_call,
            'quantity'          => $quantity,
            'status'            => 'pending',
            'notes'             => $data['notes'] ?? null,
        ]);
    }

    /**
     * Preview price without creating a booking.
     */
    public function previewPrice(int $mediaId, string $startDate, string $endDate, int $quantity = 1): array
    {
        $media = Media::findOrFail($mediaId);

        $available = $this->availabilityService->isAvailable($media->id, $startDate, $endDate);
        $pricing   = $this->pricingService->explain($media, $startDate, $endDate, $quantity);

        return [
            'media'     => $media,
            'available' => $available,
            'pricing'   => $pricing,
        ];
    }

    /**
     * Update booking status (admin use).
     */
    public function updateStatus(int $bookingId, string $status): Booking
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['status' => $status]);
        return $booking;
    }
}
