<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Jobs\SendProductNotification;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'article', 'name', 'status', 'data'
    ];
    
    protected $casts = [
        'data' => 'array'
    ];

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // protected static function booted()
    // {
    //     static::created(function ($product) {
    //         SendProductNotification::dispatch($product);
    //     });
    // }
}
