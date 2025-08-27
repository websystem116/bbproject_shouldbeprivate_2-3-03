<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireContent extends Model
{
    //
    protected $guarded = ["id"];

    // hasmany questionnaire_decisions
    public function questionnaire_decisions()
    {
        return $this->hasMany('App\QuestionnaireDecision', 'questionnaire_contents_id');
    }
}
