<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    protected $primaryKey = 'id';

    /**
     * 一括代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'student_no',
        'sale_id',
        'charge_month',
        'carryover',
        'month_sum',
        'month_tax_sum',
        'prepaid',
        'sum',
        'withdrawal_created_flg',
        'notification_sent_flg',
        'withdrawal_confirmed',
        'grade',
        'student_name',
        'school_building_name',
        'recipient_zip_code',
        'recipient_address1',
        'recipient_address2',
        'recipient_address3',
        'recipient_surname',
        'recipient_name',
        'display_message',
        'applied_discount_name',
        'creator',
        'updater',
        'sales_number',
        'read_at',
    ];

    /**
     * 日付属性
     *
     * @var array
     */
    protected $dates = [
        'email_verified_at',
        'deleted_at',
    ];

    /**
     * リレーション: InvoiceDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_no', 'student_no');
    }

    /**
     * 指定された受講授業名を取得する
     *
     * @return string|null 受講授業名
     */
    public function getClassNameByInvoiceId()
    {
        $invoice = Invoice::find($this->id);
        if ($invoice === null) {
            return '';
        }

        $invoiceDetail = optional($invoice->invoiceDetails)->first();
        return optional($invoiceDetail->product)->name;
    }
}
