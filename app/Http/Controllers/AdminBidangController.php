<?php

namespace App\Http\Controllers;

class AdminBidangController extends Controller
{
    // Mengarah ke resources/views/admin_bidang/tindaklanjuti.blade.php
    public function index()
    {
        return view('admin_bidang.tindaklanjuti');
    }
}
