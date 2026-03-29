<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }

        .wrapper { padding: 40px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; border-bottom: 3px solid #6C63FF; padding-bottom: 20px; }
        .brand h1 { font-size: 26px; color: #6C63FF; letter-spacing: 1px; }
        .brand p  { color: #888; font-size: 11px; margin-top: 3px; }
        .meta     { text-align: right; }
        .meta .badge { background: #6C63FF; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .meta p   { margin-top: 8px; color: #555; font-size: 11px; }

        /* Info grid */
        .info-grid { display: flex; gap: 24px; margin-bottom: 28px; }
        .info-card { flex: 1; background: #f8f7ff; border-left: 4px solid #6C63FF; padding: 14px 16px; border-radius: 4px; }
        .info-card h4 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #6C63FF; margin-bottom: 8px; }
        .info-card p  { font-size: 12px; color: #333; line-height: 1.7; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        thead tr { background: #6C63FF; color: #fff; }
        thead th { padding: 10px 14px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background: #f8f7ff; }
        tbody td { padding: 10px 14px; font-size: 12px; border-bottom: 1px solid #e8e8f0; }

        /* Totals */
        .totals { margin-left: auto; width: 280px; }
        .totals table { margin-bottom: 0; }
        .totals td { padding: 7px 14px; font-size: 13px; }
        .totals tr:last-child td { font-size: 15px; font-weight: bold; background: #6C63FF; color: #fff; border-radius: 4px; }

        /* Status badge */
        .status-confirmed { color: #10b981; font-weight: bold; }
        .status-pending   { color: #f59e0b; font-weight: bold; }

        /* Footer */
        .footer { margin-top: 40px; border-top: 1px solid #eee; padding-top: 16px; text-align: center; color: #aaa; font-size: 10px; }
        .footer strong { color: #6C63FF; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- HEADER -->
    <div class="header">
        <div class="brand">
            <h1>DODO Digital</h1>
            <p>Media Booking Platform</p>
        </div>
        <div class="meta">
            <div class="badge">Tax Invoice</div>
            <p><strong>Invoice #:</strong> {{ $invoiceNumber }}</p>
            <p><strong>Date:</strong> {{ $generatedAt }}</p>
            <p><strong>Ref:</strong> {{ $booking->booking_reference }}</p>
        </div>
    </div>

    <!-- BILLED TO / MEDIA INFO -->
    <div class="info-grid">
        <div class="info-card">
            <h4>Billed To</h4>
            <p>
                <strong>{{ $booking->user->name }}</strong><br/>
                {{ $booking->user->email }}
            </p>
        </div>
        <div class="info-card">
            <h4>Vendor / Supplier</h4>
            <p>
                <strong>{{ $booking->media->vendor->agency_name ?? 'N/A' }}</strong><br/>
                {{ $booking->media->vendor->phone ?? '' }}
            </p>
        </div>
        <div class="info-card">
            <h4>Booking Status</h4>
            <p>
                <span class="status-{{ $booking->status }}">{{ strtoupper($booking->status) }}</span><br/>
                @if($booking->payment)
                    Payment: {{ strtoupper($booking->payment->status) }}<br/>
                    @if($booking->payment->method)
                        Method: {{ strtoupper($booking->payment->method) }}
                    @endif
                @endif
            </p>
        </div>
    </div>

    <!-- BOOKING DETAILS TABLE -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Location</th>
                <th>Period</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <strong>{{ $booking->media->title }}</strong><br/>
                    <small style="color:#888">{{ $booking->media->media_type }} — {{ $booking->media->city }}</small>
                </td>
                <td>{{ $booking->media->location }}</td>
                <td>
                    {{ $booking->start_date->format('d M Y') }}<br/>
                    to {{ $booking->end_date->format('d M Y') }}
                </td>
                <td>{{ $booking->quantity }}</td>
                <td>
                    @if($booking->media->price_on_call)
                        Quoted
                    @else
                        ₹{{ number_format($booking->media->base_price, 2) }}/{{ $booking->media->pricing_type }}
                    @endif
                </td>
                <td>₹{{ number_format($booking->payable_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td style="text-align:right">₹{{ number_format($booking->payable_amount, 2) }}</td>
            </tr>
            <tr>
                <td>GST (18%)</td>
                <td style="text-align:right">₹{{ number_format($booking->payable_amount * 0.18, 2) }}</td>
            </tr>
            <tr>
                <td>Total Payable</td>
                <td style="text-align:right">₹{{ number_format($booking->payable_amount * 1.18, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- PAYMENT INFO -->
    @if($booking->payment && $booking->payment->razorpay_payment_id)
    <div style="margin-top:24px; padding:12px 16px; background:#f0fdf4; border-left: 4px solid #10b981; border-radius:4px;">
        <strong style="color:#10b981">✓ Payment Confirmed</strong><br/>
        <small style="color:#555">Razorpay Payment ID: {{ $booking->payment->razorpay_payment_id }}</small>
    </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <p>This is a computer-generated invoice. No signature required.</p>
        <p style="margin-top:4px">Thank you for choosing <strong>DODO Digital</strong> — the smart way to book advertising media.</p>
    </div>

</div>
</body>
</html>
