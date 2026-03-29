<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Generate a PDF invoice for a confirmed booking,
     * save it to storage/app/private/invoices/, and update the booking record.
     */
    public function generate(Booking $booking): string
    {
        $booking->load(['user', 'media', 'media.vendor', 'payment']);

        $invoiceNumber = 'INV-' . strtoupper(Str::random(8)) . '-' . $booking->id;
        $filename      = $invoiceNumber . '.pdf';
        $storagePath   = 'invoices/' . $filename;

        $pdf = Pdf::loadView('invoices.booking_invoice', [
            'booking'       => $booking,
            'invoiceNumber' => $invoiceNumber,
            'generatedAt'   => now()->format('d M Y, h:i A'),
        ]);

        // Save to private storage (not public)
        Storage::put($storagePath, $pdf->output());

        // Update booking with invoice info
        $booking->update([
            'invoice_number' => $invoiceNumber,
            'invoice_path'   => $storagePath,
        ]);

        return $storagePath;
    }

    /**
     * Return raw PDF content for download.
     */
    public function download(Booking $booking): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$booking->invoice_path || !Storage::exists($booking->invoice_path)) {
            abort(404, 'Invoice not found.');
        }

        return Storage::download($booking->invoice_path, $booking->invoice_number . '.pdf');
    }
}
