<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\SalaryInvoice;

class SalaryInvoiceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $salaryInvoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SalaryInvoice $salaryInvoice)
    {
        $this->salaryInvoice = $salaryInvoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.salary_invoice_notification')
            ->subject('【進学ゼミナールグループ】マイページ更新のお知らせ');
    }
}