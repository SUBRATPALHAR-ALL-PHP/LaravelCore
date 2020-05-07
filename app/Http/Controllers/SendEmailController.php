<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
/** for sending mails */
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubratTest;
use App\Jobs\EmailJob;

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
      $user->email=env('MY_EMAIL');

      // $Test = Mail::to($user)->send(new SubratTest());
      // ->later($when, new SubratTest());

      EmailJob::dispatch( Mail::to($user)->send(new SubratTest()));
      // dispatch(function () {Mail::to(env('MY_EMAIL'))->send(new SubratTest());})->delay(now()->addMinutes(1));

    }
}
