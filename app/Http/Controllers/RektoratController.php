<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RektoratController extends Controller
{
    public function suratMasuk()
    {
        return view('pages.rektorat.surat-masuk.index');
    }

    public function suratTugas()
    {
        return view('pages.rektorat.surat-tugas.index');
    }

    public function inboxSuratTugas()
    {
        return view('pages.rektorat.inbox-surat-tugas.index');
    }

    public function historyDisposisi()
    {
        return view('pages.rektorat.history-disposisi.index');
    }

    public function tindakLanjutSuratTugas()
    {
        return view('pages.rektorat.tindaklanjut-surat-tugas.index');
    }

    public function arsipSuratTugas()
    {
        return view('pages.rektorat.arsip-surat-tugas.index');
    }
}
