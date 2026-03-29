<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'vendor_id', 'title', 'media_type', 'city', 'location',
        'latitude', 'longitude', 'size', 'description',
        'base_price', 'price_on_call', 'pricing_type', 'status'
    ];

    protected $casts = [
        'price_on_call' => 'boolean',
        'base_price'    => 'float',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function images()
    {
        return $this->hasMany(MediaImage::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
