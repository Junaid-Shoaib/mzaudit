<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'ledger', 'statement', 'confirmation', 'enabled', 'account_id', 'year_id', 'company_id'
    ];

    public function bankAccount()
    {
        return $this->belongsTo('App\Models\BankAccount', 'account_id');
    }

    public function year()
    {
        return $this->belongsTo('App\Models\Year', 'year_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
