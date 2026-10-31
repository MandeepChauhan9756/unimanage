<?php

namespace App\Controllers;

class Home extends BaseController
{
    // Purpose: Displays the application welcome page.
    public function index(): string
    {
        return view('welcome_message');
    } 
    // End of index()
}
