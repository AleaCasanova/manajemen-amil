<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /**
     * Tampilkan daftar pengumuman resmi yayasan
     */
    public function index()
    {
        $user = Auth::user();
        $amil = $user->amil;

        $pengumumans = Pengumuman::whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now())
            ->with(['creator', 'pembacaUsers'])
            ->latest('published_at')
            ->paginate(10);

        return view('amil.pengumuman.index', compact('amil', 'pengumumans', 'user'));
    }

    /**
     * Detail pengumuman & catat keterbacaan otomatis
     */
    public function show(Pengumuman $pengumuman)
    {
        $user = Auth::user();
        $amil = $user->amil;

        // Tandai otomatis telah dibaca oleh user ini
        if (! $pengumuman->pembacaUsers->contains($user->id)) {
            $pengumuman->pembacaUsers()->attach($user->id, [
                'read_at' => Carbon::now(),
            ]);
        }

        $pengumuman->load(['creator', 'pembacaUsers']);

        return view('amil.pengumuman.show', compact('amil', 'pengumuman', 'user'));
    }
}
