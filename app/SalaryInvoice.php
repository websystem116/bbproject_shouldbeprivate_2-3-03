<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaryInvoice extends Model
{
    protected $primaryKey = 'id';
    
    /**
     * 一括代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'tightening_date',
        // 'other_payment_amount',
        // 'other_payment_reason',
        // 'other_deduction_amount',
        // 'other_deduction_reason',
        // 'transportation_expenses',
        'salary',
        // 'year_end_adjustment',
        'monthly_completion',
        'monthly_approval',
        'salary_approval',
        'monthly_tightening',
        'attendance_date',
        'municipal_tax',
        'user_name', 
        'user_name_kana', 
        'email', 
        'address1', 
        'address2', 
        'address3', 
        'post_code', 
        'tel', 
        'school_building', 
        'recipient_name', 
        'roles'
    ];

    /**
     * リレーション: salaryInvoiceDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salaryInvoiceDetails()
    {
        return $this->hasMany(SalaryInvoiceDetail::class, 'salary_invoice_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 
     *
     * @return string|null 受講授業名
     */
  
}
