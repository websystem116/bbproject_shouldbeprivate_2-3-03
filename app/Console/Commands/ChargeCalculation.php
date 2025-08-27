<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChargeCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:chargeCalculations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '請求データを再計算する';

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
        $charge_calculation_controller = app()->make('App\Http\Controllers\Api\ChargeCalculationController');
        $charge_calculation_controller->chargeCalculation();
        return 0;
    }
}
