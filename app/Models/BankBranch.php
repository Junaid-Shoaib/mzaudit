<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'address', 'enabled', 'bank_id'
    ];

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'bank_id');
    }

    public function bankAccounts()
    {
        return $this->hasMany('App\Models\BankAccount', 'branch_id');
    }

    public function bankConfirmations()
    {
        return $this->hasMany('App\Models\BankConfirmation', 'branch_id');
    }
}
