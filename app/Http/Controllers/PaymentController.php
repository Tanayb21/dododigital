<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $invoiceService;

    public function __construct(PaymentService $paymentService, InvoiceService $invoiceService)
    {
        $this->paymentService = $paymentService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * POST /payments/create-order
     * Create a Razorpay order for a booking.
     * Works for both normal + price_on_call bookings (once vendor quotes the price).
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        if ($booking->status === 'confirmed') {
            return response()->json(['message' => 'This booking is already paid and confirmed.'], 422);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Cannot pay for a cancelled booking.'], 422);
        }

        try {
            $order = $this->paymentService->createOrder($booking);

            return response()->json([
                'message'   => 'Razorpay order created. Proceed to payment.',
                'order_id'  => $order['order_id'],
                'amount'    => $order['amount'],
                'currency'  => $order['currency'],
                'key_id'    => $order['key_id'],
                'booking'   => [
                    'id'                => $booking->id,
                    'reference'         => $booking->booking_reference,
                    'price_on_call'     => $booking->price_on_call,
                    'vendor_quoted_price' => $booking->vendor_quoted_price,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /payments/verify
     * Verify Razorpay signature, confirm booking, generate invoice.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        try {
            $booking = $this->paymentService->verifyPayment(
                $request->only('razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'),
                $this->invoiceService
            );

            return response()->json([
                'message'        => '🎉 Payment successful! Booking confirmed.',
                'booking'        => $booking,
                'invoice_number' => $booking->invoice_number,
                'status'         => $booking->status,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /payments/failed
     * Frontend calls this when Razorpay modal reports failure.
     */
    public function failed(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
        ]);

        $this->paymentService->markFailed($request->razorpay_order_id);

        return response()->json(['message' => 'Payment failure recorded. Booking remains pending.']);
    }

    /**
     * GET /payments/{bookingId}
     * Get payment status for a booking.
     */
    public function show(Request $request, $bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $request->user()->id)
            ->with('payment')
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        return response()->json([
            'booking_reference' => $booking->booking_reference,
            'status'            => $booking->status,
            'payment'           => $booking->payment,
            'payable_amount'    => $booking->payable_amount,
            'invoice_number'    => $booking->invoice_number,
        ]);
    }

    /**
     * GET /payments/{bookingId}/invoice
     * Download the PDF invoice for a confirmed booking.
     */
    public function downloadInvoice(Request $request, $bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'Invoice only available for confirmed bookings.'], 422);
        }

        return $this->invoiceService->download($booking);
    }
}
