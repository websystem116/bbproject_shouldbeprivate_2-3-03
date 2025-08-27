<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryInvoiceDetail extends Model
{

    use SoftDeletes;

    protected $table = 'salary_invoice_details';

    protected $fillable = [
        'salary_invoice_id',
        'job_description_name',
        'payment_amount',
        'hourly_wage',
        'attendance_date',
        'division_name',
        'municipal_tax',
        'deduction',
        'salary_sabtotal',
        'income_tax_cost',
        'transportation_expenses',
        'other_payment_amount',
        'other_deduction_amount',
        'year_end_adjustment'
    ];

    /**
     * リレーション: SalaryInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salaryInvoice()
    {
        return $this->belongsTo(SalaryInvoice::class, 'salary_invoice_id');
    }

     public function jobDescriptions()
    {
        return $this->hasMany(jobDescription::class,  'id', 'job_description_id');
    }
}
