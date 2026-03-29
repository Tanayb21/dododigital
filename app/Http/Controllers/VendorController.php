<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\VendorService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    protected $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * POST /vendor/register
     * Authenticated user registers as a vendor.
     */
    public function register(Request $request)
    {
        $request->validate([
            'agency_name' => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
        ]);

        try {
            $vendor = $this->vendorService->registerVendor(
                $request->user()->id,
                $request->only('agency_name', 'phone')
            );

            return response()->json([
                'message' => 'Vendor registration submitted. Awaiting admin approval.',
                'vendor'  => $vendor,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /vendor/profile
     * Fetch the authenticated vendor's profile.
     */
    public function profile(Request $request)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'No vendor profile found.'], 404);
        }

        return response()->json($vendor->load('media'));
    }

    /**
     * GET /vendor/bookings
     * Vendor sees all bookings on their media inventory.
     */
    public function myBookings(Request $request)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        $mediaIds = $vendor->media()->pluck('id');

        $bookings = Booking::whereIn('media_id', $mediaIds)
            ->with(['user', 'media', 'payment'])
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    /**
     * PATCH /vendor/bookings/{id}/quote-price
     * Vendor sets a confirmed price for a price_on_call booking.
     * After this, the customer can proceed to pay via Razorpay.
     */
    public function quotePrice(Request $request, $id)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        $request->validate([
            'quoted_price' => 'required|numeric|min:1',
        ]);

        // Ensure this booking is for this vendor's media
        $mediaIds = $vendor->media()->pluck('id');
        $booking  = Booking::whereIn('media_id', $mediaIds)->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found or not yours.'], 404);
        }

        if (!$booking->price_on_call) {
            return response()->json(['message' => 'This booking is not a price-on-call booking.'], 422);
        }

        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Can only quote price for pending bookings.'], 422);
        }

        $booking->update(['vendor_quoted_price' => $request->quoted_price]);

        return response()->json([
            'message'             => 'Price quoted successfully. Customer can now proceed to pay.',
            'booking_reference'   => $booking->booking_reference,
            'vendor_quoted_price' => $booking->vendor_quoted_price,
        ]);
    }
}
