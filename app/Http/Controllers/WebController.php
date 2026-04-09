<?php

namespace App\Http\Controllers;

class WebController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function login()
    {
        return view('login');
    }

    public function carga()
    {
        return view('carga');
    }

    public function resultado()
    {
        return view('resultado');
    }
}
