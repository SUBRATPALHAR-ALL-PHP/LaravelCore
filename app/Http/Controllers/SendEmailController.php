<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
/** for sending mails */
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubratTest;

class SendEmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() { }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
      $user = new \stdClass();
      $user->email='subratpalhar92@gmail.com';
      $Test = Mail::to($user)->send(new SubratTest());
    }
}
