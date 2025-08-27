<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Invoice;

class InvoiceDetail extends Model
{
    use SoftDeletes;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'invoice_details';

    /**
     * 一括代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'student_no',
        'sale_month',
        'invoice_id',
        'product_id',
        'charges_date',
        'product_name',
        'product_price',
        'product_price_display',
        'price',
        'tax',
        'subtotal',
        'remarks',
        'creator',
        'updater',
        'sales_number',
        'division_name', // 授業料（クラス）カラム名を修正
    ];

    /**
     * 日付属性
     *
     * @var array
     */
    protected $dates = [
        'charges_date',
        'deleted_at',
    ];

    /**
     * リレーション: Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * リレーション: Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
