<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'label', 'value', 'type', 'is_secret', 'sort_order'];

    protected $casts = ['is_secret' => 'boolean'];
}
