<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryGroup extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];
    protected $casts    = ['is_active' => 'boolean'];

    public function categories()
    {
        return $this->hasMany(Category::class)->where('is_active', true)->orderBy('sort_order');
    }
}
