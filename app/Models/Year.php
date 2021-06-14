<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $fillable = [
        'begin', 'end', 'enabled', 'company_id'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function bankBalances()
    {
        return $this->hasMany('App\Models\BankBalance', 'year_id');
    }

    public function bankConfirmations()
    {
        return $this->hasMany('App\Models\BankConfirmation', 'year_id');
    }
}
