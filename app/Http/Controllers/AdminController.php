<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Media;
use App\Services\BookingService;
use App\Services\VendorService;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $vendorService;
    protected $mediaService;
    protected $bookingService;

    public function __construct(
        VendorService $vendorService,
        MediaService $mediaService,
        BookingService $bookingService
    ) {
        $this->vendorService  = $vendorService;
        $this->mediaService   = $mediaService;
        $this->bookingService = $bookingService;
    }

    /**
     * GET /admin/vendors
     * List all vendors.
     */
    public function listVendors()
    {
        $vendors = Vendor::with('user')->get();
        return response()->json($vendors);
    }

    /**
     * PATCH /admin/vendor/{id}/approve
     */
    public function approveVendor($id)
    {
        $vendor = $this->vendorService->approveVendor($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found.'], 404);
        }

        return response()->json(['message' => 'Vendor approved.', 'vendor' => $vendor]);
    }

    /**
     * PATCH /admin/vendor/{id}/reject
     */
    public function rejectVendor($id)
    {
        $vendor = $this->vendorService->rejectVendor($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found.'], 404);
        }

        return response()->json(['message' => 'Vendor rejected.', 'vendor' => $vendor]);
    }

    /**
     * PATCH /admin/media/{id}/status
     * Set media to active or inactive.
     */
    public function setMediaStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $media = $this->mediaService->setStatus($id, $request->status);

        if (!$media) {
            return response()->json(['message' => 'Media not found.'], 404);
        }

        return response()->json(['message' => 'Media status updated.', 'media' => $media]);
    }

    /**
     * POST /admin/login-as/{userId}
     * Secret login: Admin impersonates a vendor user.
     */
    public function loginAs(Request $request, $userId)
    {
        $targetUser = User::findOrFail($userId);

        if ($targetUser->role === 'admin') {
            return response()->json(['message' => 'Cannot impersonate another admin.'], 403);
        }

        // Use session-based authentication for the impersonated user
        Auth::guard('web')->login($targetUser);
        $request->session()->regenerate();

        return response()->json([
            'message' => "Logged in as {$targetUser->name} (impersonation)",
            'user'    => $targetUser,
        ]);
    }

    /**
     * GET /admin/bookings
     * List all bookings with user and media info.
     */
    public function listBookings(Request $request)
    {
        $bookings = Booking::with(['user', 'media'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    /**
     * PATCH /admin/bookings/{id}/status
     * Confirm or cancel a booking.
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking = $this->bookingService->updateStatus($id, $request->status);

        return response()->json([
            'message' => 'Booking status updated.',
            'booking' => $booking,
        ]);
    }
}
