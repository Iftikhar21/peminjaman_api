<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusBorrower extends Model
{
    protected $table = 'status_borrower';

    protected $fillable = [
        'status_name',
    ];

    public function userDetail()
    {
        return $this->hasMany(UserDetail::class);
    }
}
