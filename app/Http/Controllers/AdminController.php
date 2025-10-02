<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function pengguna()
    {
        return view('pages.admin.pengguna.index');
    }

    public function departemen()
    {
        return view('pages.admin.departemen.index');
    }

    public function jenisSurat()
    {
        return view('pages.admin.jenis-surat.index');
    }

    public function monitoring()
    {
        return view('pages.admin.monitoring.index');
    }
}
