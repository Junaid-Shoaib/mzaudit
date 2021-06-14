<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sent', 'confirm_create', 'reminder', 'received', 'enabled', 'branch_id', 'year_id', 'company_id'
    ];

    public function bankBranch()
    {
        return $this->belongsTo('App\Models\BankBranch', 'branch_id');
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
