<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','enabled'
    ];

    public function bankBranches()
    {
        return $this->hasMany('App\Models\BankBranch', 'bank_id');
    }

}
