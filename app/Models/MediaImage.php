<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaImage extends Model
{
    protected $fillable = ['media_id', 'image_url'];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
