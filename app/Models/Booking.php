<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'media_id',
        'booking_reference',
        'start_date',
        'end_date',
        'total_price',
        'vendor_quoted_price',
        'price_on_call',
        'quantity',
        'status',
        'notes',
        'invoice_number',
        'invoice_path',
    ];

    protected $casts = [
        'start_date'         => 'date',
        'end_date'           => 'date',
        'price_on_call'      => 'boolean',
        'total_price'        => 'float',
        'vendor_quoted_price'=> 'float',
    ];

    /** The final payable amount — vendor_quoted_price takes priority for price_on_call bookings */
    public function getPayableAmountAttribute(): ?float
    {
        if ($this->price_on_call) {
            return $this->vendor_quoted_price;
        }
        return $this->total_price;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
