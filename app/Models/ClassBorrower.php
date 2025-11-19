<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassBorrower extends Model
{
    protected $table = 'class';

    protected $fillable = [
        'class_name'
    ];

    public function userDetail()
    {
        return $this->hasMany(UserDetail::class);
    }
}
