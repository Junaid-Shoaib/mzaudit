<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'currency', 'enabled', 'branch_id', 'company_id'
    ];

    public function bankBranch()
    {
        return $this->belongsTo('App\Models\BankBranch', 'branch_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function bankBalances()
    {
        return $this->hasMany('App\Models\BankBalance', 'account_id');
    }
}
