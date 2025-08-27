<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_no', 40)->comment('生徒テーブルID');
            $table->string('sale_id')->nullable()->comment('売上テーブルID');
            $table->string('charge_month')->nullable()->comment('年月');
            $table->string('carryover')->nullable()->comment('前月繰越金');
            $table->string('month_sum')->nullable()->comment('当月明細合計');
            $table->string('month_tax_sum')->nullable()->comment('当月消費税合計');
            $table->string('prepaid')->nullable()->comment('事前入金');
            $table->string('sum')->nullable()->comment('合計請求額');
            $table->string('withdrawal_created_flg')->nullable()->comment('自動引落データ作成済みフラグ');
            $table->boolean('notification_sent_flg')->default(false);
            $table->string('withdrawal_confirmed')->nullable()->comment('引落確認');
            $table->integer('grade')->nullable()->comment('学年');
            $table->string('student_name', 250)->nullable()->comment('生徒名');
            $table->string('school_building_name')->nullable()->comment('校舎名');
            $table->string('recipient_zip_code')->nullable()->comment('請求書の宛先郵便番号');
            $table->string('recipient_address1')->nullable()->comment('請求書の宛先住所1');
            $table->string('recipient_address2')->nullable()->comment('請求書の宛先住所2');
            $table->string('recipient_address3')->nullable()->comment('請求書の宛先住所3');
            $table->string('recipient_surname')->nullable()->comment('請求書の宛先姓');
            $table->string('recipient_name')->nullable()->comment('請求書の宛名');
            $table->text('display_message')->nullable()->comment('請求書に表示する特定の文言');
            $table->string('applied_discount_name')->nullable()->comment('適用された割引の名前');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->softDeletes()->comment('削除日');
            $table->timestamps();
            $table->timestamp('read_at')->nullable()->comment('既読日時');
            $table->string('sales_number', 40)->comment('売上No 売上テーブルと紐付け');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
