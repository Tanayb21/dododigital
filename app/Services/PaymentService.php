<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\SettingService;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentService
{
    protected Api $razorpay;
    protected SettingService $settings;

    public function __construct(SettingService $settings)
    {
        $this->settings = $settings;

        // Prefer DB values, fallback to .env config
        $keyId     = $settings->get('razorpay_key_id')     ?: config('services.razorpay.key_id');
        $keySecret = $settings->get('razorpay_key_secret') ?: config('services.razorpay.key_secret');

        $this->razorpay = new Api($keyId, $keySecret);
    }

    /**
     * Create a Razorpay order for a booking.
     * Supports both normal bookings and price_on_call (after vendor quotes the price).
     *
     * @throws \Exception
     */
    public function createOrder(Booking $booking): array
    {
        $amount = $booking->payable_amount;

        if ($amount === null || $amount <= 0) {
            throw new \Exception(
                'Payment amount is not set. ' .
                ($booking->price_on_call
                    ? 'Vendor has not quoted a price yet. Please wait for the vendor to assign a price.'
                    : 'Booking has no price.')
            );
        }

        // Existing payment still pending? Return same order
        $existing = $booking->payment;
        if ($existing && $existing->status === 'pending' && $existing->razorpay_order_id) {
            return [
                'order_id'  => $existing->razorpay_order_id,
                'amount'    => $existing->amount,
                'currency'  => 'INR',
                'key_id'    => $this->settings->get('razorpay_key_id') ?: config('services.razorpay.key_id'),
                'booking'   => $booking,
            ];
        }

        // Create Razorpay order (amount in paise = ₹ × 100)
        $order = $this->razorpay->order->create([
            'amount'          => (int) ($amount * 100),
            'currency'        => 'INR',
            'receipt'         => $booking->booking_reference,
            'payment_capture' => 1,
            'notes'           => [
                'booking_id'        => $booking->id,
                'booking_reference' => $booking->booking_reference,
            ],
        ]);

        // Store as a pending payment record
        Payment::create([
            'booking_id'       => $booking->id,
            'razorpay_order_id'=> $order->id,
            'amount'           => $amount,
            'status'           => 'pending',
        ]);

        return [
            'order_id' => $order->id,
            'amount'   => $amount,
            'currency' => 'INR',
            'key_id'   => config('services.razorpay.key_id'),
            'booking'  => $booking,
        ];
    }

    /**
     * Verify Razorpay payment signature after the user pays on frontend.
     * On success: update payment, confirm booking, generate invoice.
     *
     * @throws \Exception
     */
    public function verifyPayment(array $data, InvoiceService $invoiceService): Booking
    {
        $attributes = [
            'razorpay_order_id'   => $data['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature'  => $data['razorpay_signature'],
        ];

        try {
            $this->razorpay->utility->verifyPaymentSignature($attributes);
        } catch (SignatureVerificationError $e) {
            throw new \Exception('Payment signature verification failed. Payment may be tampered.');
        }

        // Find the payment record by order ID
        $payment = Payment::where('razorpay_order_id', $data['razorpay_order_id'])->firstOrFail();

        // Idempotency — already processed
        if ($payment->status === 'success') {
            return $payment->booking;
        }

        // Fetch full payment details from Razorpay
        $rzpPayment = $this->razorpay->payment->fetch($data['razorpay_payment_id']);

        // Update payment record
        $payment->update([
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature'  => $data['razorpay_signature'],
            'status'              => 'success',
            'method'              => $rzpPayment->method ?? null,
            'raw_response'        => (array) $rzpPayment->toArray(),
        ]);

        // Confirm the booking
        $booking = $payment->booking;
        $booking->update(['status' => 'confirmed']);

        // Generate invoice PDF
        $invoiceService->generate($booking);

        return $booking->fresh(['media', 'payment']);
    }

    /**
     * Mark payment as failed (called on Razorpay webhook / frontend failure callback).
     */
    public function markFailed(string $razorpayOrderId): void
    {
        Payment::where('razorpay_order_id', $razorpayOrderId)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);
    }
}
