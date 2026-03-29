<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['category_group_id', 'name', 'subtitle', 'image_url', 'media_type_filter', 'is_active', 'sort_order'];
    protected $casts    = ['is_active' => 'boolean'];

    public function group()
    {
        return $this->belongsTo(CategoryGroup::class, 'category_group_id');
    }
}
