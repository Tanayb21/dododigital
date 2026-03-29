<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Tax rates (can be later made configurable via settings)
     */
    protected float $cgstRate = 9;
    protected float $sgstRate = 9;

    /**
     * Generate a PDF invoice for a confirmed booking,
     * save it to storage/app/private/invoices/, and update the booking record.
     */
    public function generate(Booking $booking): string
    {
        $booking->load(['user', 'media', 'media.vendor', 'payment']);

        // Sequential invoice numbering: DODO/INV/{id}
        $invoiceNumber = 'DODO/INV/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
        $filename      = 'INV-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT) . '.pdf';
        $storagePath   = 'invoices/' . $filename;

        // Tax calculations
        $subtotal   = $booking->payable_amount ?? 0;
        $cgstAmount = round($subtotal * ($this->cgstRate / 100), 2);
        $sgstAmount = round($subtotal * ($this->sgstRate / 100), 2);
        $grandTotal = $subtotal + $cgstAmount + $sgstAmount;

        $pdf = Pdf::loadView('invoices.booking_invoice', [
            'booking'        => $booking,
            'invoiceNumber'  => $invoiceNumber,
            'invoiceDate'    => now()->format('d/m/Y'),
            'placeOfSupply'  => 'Maharashtra (27)',
            'subtotal'       => $subtotal,
            'cgstRate'       => $this->cgstRate,
            'sgstRate'       => $this->sgstRate,
            'cgstAmount'     => $cgstAmount,
            'sgstAmount'     => $sgstAmount,
            'grandTotal'     => $grandTotal,
            'totalInWords'   => $this->numberToWords($grandTotal) . ' Only',
            'hsnSac'         => '998361',  // Advertising services SAC code
            // Company details (can be later pulled from settings)
            'companyAddress'  => 'Unit No. 205, 2nd Floor, Raheja Tesla Industrial, Juinagar,',
            'companyCity'     => 'Navi Mumbai, Thane, Maharashtra – 400707',
            'companyGstin'    => '27AAXFD3744K1ZS',
            'companyPhone'    => '9324446980',
            'companyEmail'    => 'info@oohdodo.com',
            'bankName'        => 'Union Bank of India, Nerul – West, Sector V Branch, Thane – 400706',
            'bankAccount'     => '089021010000156',
            'bankIfsc'        => 'UBIN0908908',
        ]);

        $pdf->setPaper('A4', 'portrait');

        // Save to private storage
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

        $downloadName = str_replace('/', '-', $booking->invoice_number) . '.pdf';
        return Storage::download($booking->invoice_path, $downloadName);
    }

    /**
     * Convert a number to Indian Rupee words.
     */
    protected function numberToWords(float $number): string
    {
        if ($number == 0) {
            return 'Zero';
        }

        $number = round($number);

        $ones = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
            'Seventeen', 'Eighteen', 'Nineteen'
        ];

        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if ($number < 0) {
            return 'Minus ' . $this->numberToWords(abs($number));
        }

        $words = '';

        // Crore (1,00,00,000)
        if (intval($number / 10000000) > 0) {
            $words .= $this->numberToWords(intval($number / 10000000)) . ' Crore ';
            $number %= 10000000;
        }

        // Lakh (1,00,000)
        if (intval($number / 100000) > 0) {
            $words .= $this->numberToWords(intval($number / 100000)) . ' Lakh ';
            $number %= 100000;
        }

        // Thousand (1,000)
        if (intval($number / 1000) > 0) {
            $words .= $this->numberToWords(intval($number / 1000)) . ' Thousand ';
            $number %= 1000;
        }

        // Hundred
        if (intval($number / 100) > 0) {
            $words .= $ones[intval($number / 100)] . ' Hundred ';
            $number %= 100;
        }

        if ($number > 0) {
            if ($words !== '') {
                $words .= 'and ';
            }

            if ($number < 20) {
                $words .= $ones[intval($number)];
            } else {
                $words .= $tens[intval($number / 10)];
                if ($number % 10 !== 0) {
                    $words .= '-' . $ones[intval($number % 10)];
                }
            }
        }

        return trim($words);
    }
}
