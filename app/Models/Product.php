<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'image', 'title', 'slug', 'category_id', 'user_id', 'description', 'weight', 'price', 'stock', 'discount'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function products() 
    {
        return $this->hasMany(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewAvgRating(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? substr($value, 0, 3) : 0,
        );
    } 

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('/storage/products/' . $value),
        );
    }


}
