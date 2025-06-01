<?php
// app/Mail/UserRegisteredMail.php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredMail extends Mailable
{
    use SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.user_registered')
                    ->with([
                        'userName' => $this->user->nama_lengkap,
                        'nik' => $this->user->nik,
                        'kontak'=> $this->user->kontak,
                    ]);
    }
}
