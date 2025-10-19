<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function Contact(){
        return view('frontend.contact.contact');
    } // End Method

    public function StoreContact(Request $request){
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        // Here you can add logic to save contact message to database
        // or send email notification

        $notification = array(
            'message' => 'Your message has been sent successfully!',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } // End Method
}
