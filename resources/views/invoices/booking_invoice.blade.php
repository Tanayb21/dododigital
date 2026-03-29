<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }

        .wrapper { padding: 30px 40px 20px; position: relative; min-height: 100%; }

        /* === HEADER === */
        .header { margin-bottom: 20px; }
        .brand-name {
            font-size: 30px;
            font-weight: 900;
            color: #E91E8C;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }
        .company-info { font-size: 11px; color: #444; line-height: 1.6; }

        /* === TAX INVOICE TITLE === */
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 18px 0 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* === INVOICE META ROW === */
        .invoice-meta {
            width: 100%;
            margin-bottom: 16px;
        }
        .invoice-meta td {
            font-size: 11px;
            color: #333;
            padding: 2px 0;
        }
        .invoice-meta .label { font-weight: normal; }

        /* === BILL TO === */
        .bill-to {
            margin-bottom: 16px;
        }
        .bill-to h4 {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .bill-to p {
            font-size: 11px;
            color: #333;
            line-height: 1.7;
        }

        /* === SUBJECT LINE === */
        .subject-line {
            margin-bottom: 16px;
            font-size: 11px;
            color: #1a1a1a;
        }
        .subject-line strong { font-weight: bold; }

        /* === ITEM TABLE === */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .items-table thead tr {
            border-top: 1.5px solid #1a1a1a;
            border-bottom: 1.5px solid #1a1a1a;
        }
        .items-table thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .items-table thead th.right { text-align: right; }
        .items-table tbody td {
            padding: 10px 10px;
            font-size: 11px;
            color: #333;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }
        .items-table tbody td.right { text-align: right; }

        /* === TOTALS === */
        .totals-section {
            width: 100%;
            margin-bottom: 18px;
        }
        .totals-section td {
            padding: 4px 10px;
            font-size: 11px;
            text-align: right;
        }
        .totals-section td.label-col {
            font-weight: normal;
            color: #333;
            width: 70%;
        }
        .totals-section td.amount-col {
            color: #1a1a1a;
            width: 30%;
        }
        .totals-section tr.grand-total td {
            font-weight: bold;
            font-size: 12px;
            color: #1a1a1a;
            padding-top: 6px;
            border-top: 1px solid #ccc;
        }

        /* === TOTAL IN WORDS === */
        .total-words {
            font-size: 11px;
            color: #333;
            margin-bottom: 18px;
        }

        /* === BANK DETAILS === */
        .bank-details {
            margin-bottom: 22px;
            font-size: 11px;
            color: #333;
            line-height: 1.7;
        }

        /* === SIGNATORY === */
        .signatory {
            margin-bottom: 10px;
        }
        .signatory p {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .signatory .spacer {
            height: 30px;
        }

        /* === FOOTER BAR === */
        .footer-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 28px;
            background: #E91E8C;
        }

        /* Page layout for PDF */
        @page {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- HEADER: Company Branding -->
    <div class="header">
        <div class="brand-name">DODO DIGITAL</div>
        <div class="company-info">
            {{ $companyAddress ?? 'Unit No. 205, 2nd Floor, Raheja Tesla Industrial, Juinagar,' }}<br/>
            {{ $companyCity ?? 'Navi Mumbai, Thane, Maharashtra – 400707' }}<br/>
            GSTIN: {{ $companyGstin ?? '27AAXFD3744K1ZS' }} | Mobile: {{ $companyPhone ?? '9324446980' }}<br/>
            Email: {{ $companyEmail ?? 'info@oohdodo.com' }}
        </div>
    </div>

    <!-- TAX INVOICE Title -->
    <div class="invoice-title">TAX INVOICE</div>

    <!-- INVOICE META: Number, Date, Place of Supply -->
    <table class="invoice-meta">
        <tr>
            <td><span class="label">Invoice No.: {{ $invoiceNumber }}</span></td>
            <td style="text-align: right;"><span class="label">Invoice Date: {{ $invoiceDate }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Place of Supply: {{ $placeOfSupply ?? 'Maharashtra (27)' }}</span></td>
            <td></td>
        </tr>
    </table>

    <!-- BILL TO / SHIP TO -->
    <div class="bill-to">
        <h4>Bill To / Ship To:</h4>
        <p>
            <strong>{{ $booking->user->name }}</strong><br/>
            @if($booking->user->email)
                {{ $booking->user->email }}<br/>
            @endif
            @if(isset($clientAddress) && $clientAddress)
                {{ $clientAddress }}<br/>
            @endif
            @if(isset($clientGstin) && $clientGstin)
                GSTIN: {{ $clientGstin }}
            @endif
        </p>
    </div>

    <!-- SUBJECT LINE -->
    <div class="subject-line">
        <strong>Subject:</strong> {{ $booking->media->title }}
        @if($booking->media->media_type)
            — {{ $booking->media->media_type }}
        @endif
        @if($booking->media->city)
            ({{ $booking->media->city }})
        @endif
    </div>

    <!-- ITEMS TABLE -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 40%;">Item & Description</th>
                <th style="width: 15%;">HSN/SAC</th>
                <th style="width: 10%;">Qty</th>
                <th class="right" style="width: 15%;">Rate</th>
                <th class="right" style="width: 15%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    {{ $booking->media->title }}
                    @if($booking->media->description)
                        <br/><small style="color:#777;">{{ Str::limit($booking->media->description, 80) }}</small>
                    @endif
                </td>
                <td>{{ $hsnSac ?? '998361' }}</td>
                <td>{{ $booking->quantity }}</td>
                <td class="right">
                    @if($booking->media->price_on_call)
                        Quoted
                    @else
                        {{ number_format($booking->media->base_price, 2) }}
                    @endif
                </td>
                <td class="right">{{ number_format($subtotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALS -->
    <table class="totals-section">
        <tr>
            <td class="label-col">Sub Total</td>
            <td class="amount-col">{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label-col">CGST @ {{ $cgstRate }}%</td>
            <td class="amount-col">{{ number_format($cgstAmount, 2) }}</td>
        </tr>
        <tr>
            <td class="label-col">SGST @ {{ $sgstRate }}%</td>
            <td class="amount-col">{{ number_format($sgstAmount, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td class="label-col">Grand Total</td>
            <td class="amount-col">{{ number_format($grandTotal, 2) }}</td>
        </tr>
    </table>

    <!-- TOTAL IN WORDS -->
    <div class="total-words">
        Total in Words: <strong>{{ $totalInWords }}</strong>
    </div>

    <!-- BANK DETAILS -->
    <div class="bank-details">
        A/c Holder Name: DODO Digital<br/>
        Bank: {{ $bankName ?? 'Union Bank of India, Nerul – West, Sector V Branch, Thane – 400706' }}<br/>
        A/c Number: {{ $bankAccount ?? '089021010000156' }} | IFSC Code: {{ $bankIfsc ?? 'UBIN0908908' }}
    </div>

    <!-- AUTHORIZED SIGNATORY -->
    <div class="signatory">
        <p>Authorized Signatory</p>
        <div class="spacer"></div>
        <p>(Seal & Signature)</p>
        <div class="spacer"></div>
        <p>DODO Digital</p>
    </div>

</div>

<!-- FOOTER BAR -->
<div class="footer-bar"></div>

</body>
</html>
