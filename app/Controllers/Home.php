<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Load homepage inside template
        return view('template', ['content' => view('index')]);
    }

    public function about(): string
    {
        // Load about page inside template
        return view('template', ['content' => view('about')]);
    }

    public function contact(): string
    {
        // Load contact page inside template
        return view('template', ['content' => view('contact')]);
    }
}
