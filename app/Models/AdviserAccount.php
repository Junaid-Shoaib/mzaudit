<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdviserAccount extends Model
{
    use HasFactory;
    protected  $fillable=['advisor_id','company_id', 'year_id'];



    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function advisor()
    {
        return $this->belongsTo('App\Models\Advisor', 'advisor_id');
    }
}

