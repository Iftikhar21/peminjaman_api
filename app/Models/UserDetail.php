<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'user_detail';

    protected $fillable = [
        'user_id',
        'identity_number',
        'phone',
        'status_id',
        'class_id',
        'major_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function status() {
        return $this->belongsTo(StatusBorrower::class);
    }
    
    public function class() {
        return $this->belongsTo(ClassBorrower::class);
    }
    public function major() {
        return $this->belongsTo(Major::class);
    }
}
