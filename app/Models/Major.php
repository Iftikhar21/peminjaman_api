<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $table = 'major';

    protected $fillable = [
        'major_name'
    ];

    public function userDetail()
    {
        return $this->hasMany(UserDetail::class);
    }
}
