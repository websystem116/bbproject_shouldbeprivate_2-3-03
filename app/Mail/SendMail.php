<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($data)
    {
        $this->name = $data['user_name'];
        $this->email = $data['user_email'];
        $this->studentName = $data['student_name'];
        $this->message_data = $data['message_data'];
    }


    public function build()
    {
        return $this->from(config('const.mail_from_addess'), '[進ゼミ 自動送信]')
            ->subject("進学ゼミナール・進ゼミ個別・進ゼミキッズ・大学受験館入退室メール")
            ->to($this->email, $this->name)
            ->text('emails.sendmail')
            ->with([
                'message_data' => $this->message_data,
            ]);
    }
}
