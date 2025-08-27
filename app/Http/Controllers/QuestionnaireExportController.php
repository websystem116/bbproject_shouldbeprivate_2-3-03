<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\QuestionnaireResultsDetail;
use App\QuestionnaireContent;
use App\SchoolBuilding;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\QuestionnaireResultsDetailExport;

class QuestionnaireExportController extends Controller
{
    //Export Excel
    public function export()
    {
        return Excel::download(new QuestionnaireResultsDetailExport, 'questionnaire_results_detail.xlsx');
    }
}
