<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'product_name',
        'category_id',
        'qty'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
