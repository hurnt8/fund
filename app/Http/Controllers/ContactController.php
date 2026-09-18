<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Mail\SubscribeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendMail(Request $request){
        $request->validate([
            'name' => 'required|string|min:1',
            'email' => 'required|email|min:1',
            'subject' => 'required|string|min:1',
            'message' => 'required|string|min:1',
        ]);
        $data = $request->all();
        Mail::to(config('mail.contact_address'))->send(new ContactMail([
            'name' => $data['name'],
            'message' => $data['message'],
            'email' => $data['email'],
            'subject' => $data['subject'],
        ]));
        return back()->with('success', 'message sent successfully');
    }

    public function subscribeMail(Request $request){
        $request->validate([
            'email' => 'required|email|min:1',
        ]);
        $data = $request->all();
        Mail::to(config('mail.contact_address'))->send(new SubscribeMail([
            'email' => $data['email'],
        ]));
        return back()->with('success', 'Subscription successfully completed');
    }

}
