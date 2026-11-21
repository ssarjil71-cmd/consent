<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the Terms and Conditions page
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Show the Privacy Policy page
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Show the Refund Policy page
     */
    public function refund()
    {
        return view('pages.refund');
    }
}
