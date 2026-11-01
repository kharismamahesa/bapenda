<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\HalamanKonten;

class PublicController extends Controller
{
    public function beranda()
    {
        $berita = Berita::with('kategori')
            ->where('status', 1)
            ->orderByDesc('tanggal_berita')
            ->orderByDesc('id_berita')
            ->take(3)
            ->get();

        $profil = HalamanKonten::where('slug', 'profil')->first();

        return view('public.home', compact('berita', 'profil'));
    }

    public function halaman($slug)
    {
        $halaman = HalamanKonten::where('slug', $slug)->firstOrFail();
        $halamanList = HalamanKonten::orderBy('id')->get();

        return view('public.halaman', compact('halaman', 'halamanList'));
    }

    public function beritaIndex()
    {
        $berita = Berita::with('kategori')
            ->where('status', 1)
            ->orderByDesc('tanggal_berita')
            ->orderByDesc('id_berita')
            ->paginate(9);

        return view('public.berita.index', compact('berita'));
    }

    public function beritaShow($slug)
    {
        $berita = Berita::with('kategori')
            ->where('status', 1)
            ->where(function ($query) use ($slug) {
                $query->where('url', $slug);

                if (is_numeric($slug)) {
                    $query->orWhere('id_berita', (int) $slug);
                }
            })
            ->firstOrFail();

        $berita->increment('counter');

        $beritaTerkait = Berita::with('kategori')
            ->where('status', 1)
            ->where('id_berita', '!=', $berita->id_berita)
            ->orderByDesc('tanggal_berita')
            ->take(3)
            ->get();

        return view('public.berita.show', compact('berita', 'beritaTerkait'));
    }
}
