<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdviserConfirmation extends Model
{
    use HasFactory;
    protected $fillable = [
        'sent', 'confirm_create', 'reminder', 'received', 'path' ,'enabled', 'advisor_id', 'year_id', 'company_id'
    ];

    public function advisorAccount()
    {
        return $this->belongsTo('App\Models\AdviserAccount', 'advisor_id');
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
