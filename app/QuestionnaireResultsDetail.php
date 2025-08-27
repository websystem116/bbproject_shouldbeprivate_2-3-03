<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireResultsDetail extends Model
{
    //
    protected $guarded = ["id"];


    //questionnaire_content
    public function questionnaire_content()
    {
        return $this->belongsTo('App\QuestionnaireContent', 'questionnaire_content_id');
    }
    public function questionnaire_every_subjects()
    {
        return $this->hasMany('App\QuestionnaireEverySubject', 'questionnaire_results_details_id');
    }
    //school_building
    public function school_building()
    {
        return $this->belongsTo('App\SchoolBuilding', 'school_building_id');
    }
}
