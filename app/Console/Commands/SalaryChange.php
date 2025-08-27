<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SalaryChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary-change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '給与の調整コマンド';

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
        $salary_change_controller = app()->make('App\Http\Controllers\Api\SalaryChangeController');
        $salary_change_controller->salalychange();

        return 0;
    }
}
