<?php

namespace App\Exports;

use App\QuestionnaireResultsDetail;
use Maatwebsite\Excel\Concerns\FromCollection;

class questionnaire_results_detailsExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return QuestionnaireResultsDetail::all();
    }
}
