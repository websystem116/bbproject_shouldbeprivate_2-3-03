<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue; // これは必要に応じて
use App\Student;

class StudentRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $plainPassword;
    public $mypageUrl; // マイページURLを追加

    /**
     * Create a new message instance.
     *
     * @param  Student  $student
     * @param  string  $plainPassword
     * @return void
     */
    public function __construct(Student $student, string $plainPassword)
    {
        $this->student = $student;
        $this->plainPassword = $plainPassword;
        $this->mypageUrl = config('app.mypage_url'); // .envのMYPAGE_URLをconfig経由で取得
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.student_registration')
            ->subject('【(株)進学ゼミナール】個人マイページの設定お知らせ');
    }
}
