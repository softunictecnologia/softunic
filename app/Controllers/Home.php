<?php

namespace App\Controllers;

use Config\Email;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function email()
    {


        $email = service('email');

        $email->setFrom('your@example.com', 'Your Name');
        $email->setTo('eletronicapauly@gmail.com');
        // $email->setCC('another@another-example.com');
        // $email->setBCC('them@their-example.com');

        $email->setSubject('Email Test');
        $email->setMessage('Testing the email class.');

        if ($email->send()) {
            echo 'Email enviado';
        } else {
            $email->printDebugger();
        }
    }
}
