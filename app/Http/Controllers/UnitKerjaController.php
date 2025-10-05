<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    public function arsipSuratTugas()
    {
        return view('pages.unit_kerja.arsip-surat-tugas.index');
    }

    public function buatSurat()
    {
        return view('pages.unit_kerja.buat-surat.index');
    }

    public function suratMasuk()
    {
        return view('pages.unit_kerja.surat-masuk.index');
    }
}
