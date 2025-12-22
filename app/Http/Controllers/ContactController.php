<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminContactNotification;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string',
            'email'   => 'required|email',
            'phone'   => 'required|string',
            'message' => 'required|string',
        ]);

        // Direct email set here
        Mail::to('yoursitefactory@gmail.com')
            ->send(new AdminContactNotification($validated));

        return back()->with('success', 'Message sent successfully!');
    }
}
