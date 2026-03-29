<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * GET /bookings
     * List the authenticated user's bookings.
     */
    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with(['media', 'media.images'])
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    /**
     * GET /bookings/{id}
     * Get detail of a specific booking (must belong to user).
     */
    public function show(Request $request, $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)
            ->with(['media', 'media.images', 'media.vendor'])
            ->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        return response()->json($booking);
    }

    /**
     * GET /bookings/preview
     * Preview price & availability before creating a booking.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'media_id'   => 'required|exists:media,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'quantity'   => 'sometimes|integer|min:1',
        ]);

        try {
            $result = $this->bookingService->previewPrice(
                $request->media_id,
                $request->start_date,
                $request->end_date,
                $request->integer('quantity', 1)
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /bookings
     * Create a new booking.
     */
    public function store(Request $request)
    {
        $request->validate([
            'media_id'   => 'required|exists:media,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'quantity'   => 'sometimes|integer|min:1',
            'notes'      => 'nullable|string|max:500',
        ]);

        try {
            $booking = $this->bookingService->createBooking(
                $request->user()->id,
                $request->only('media_id', 'start_date', 'end_date', 'quantity', 'notes')
            );

            $booking->load(['media', 'media.images']);

            return response()->json([
                'message' => $booking->price_on_call
                    ? 'Booking created. Price is "on call" — the vendor will contact you with a quote.'
                    : 'Booking created successfully.',
                'booking'          => $booking,
                'booking_reference' => $booking->booking_reference,
                'total_price'      => $booking->total_price,
                'price_on_call'    => $booking->price_on_call,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * DELETE /bookings/{id}
     * Cancel a booking (user can only cancel their own pending bookings).
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Only pending bookings can be cancelled.'], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking cancelled successfully.', 'booking' => $booking]);
    }
}
