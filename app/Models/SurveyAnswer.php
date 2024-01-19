<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    //when this model is used to connect to database, an error will appear due not not having timestamps fields
    //to fix this
    const CREATED_AT = null;
    const UPDATED_AT = null;
    
    protected $fillable = ['survey_id', 'start_date', 'end_date'];
    use HasFactory;

    //RELATIONSHIPS
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
