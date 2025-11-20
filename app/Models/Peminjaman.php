<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'product_id',
        'location_id',
        'start_date',
        'end_date',
        'pin_code',
        'note',
        'status',
        'qty'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function location() {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
