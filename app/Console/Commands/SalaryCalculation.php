<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SalaryCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:salaryCalculations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '非常勤の給与を再計算する';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $salary_calculation_controller = app()->make('App\Http\Controllers\Api\SalaryCalculationController');
        $salary_calculation_controller->salalyCalculation(); 

        return 0;

    }
}
