<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    /**
     * Tampilkan agenda penugasan kerja amil
     */
    public function index(Request $request)
    {
        $amil = Auth::user()->amil;

        $query = Agenda::where(function ($q) use ($amil) {
            $q->whereHas('amils', function ($sub) use ($amil) {
                $sub->where('amils.id', $amil->id);
            })->orWhereDoesntHave('amils'); // Agenda umum jika tidak spesifik amil
        })->with(['amils']);

        // Filter tab: aktif (terjadwal/berlangsung) atau selesai
        $tab = $request->get('tab', 'mendatang');

        if ($tab === 'selesai') {
            $query->where(function ($q) {
                $q->where('status', 'selesai')
                    ->orWhere('selesai', '<', Carbon::now());
            })->latest('selesai');
        } else {
            $query->where(function ($q) {
                $q->whereIn('status', ['terjadwal', 'berlangsung'])
                    ->where('selesai', '>=', Carbon::now());
            })->orderBy('mulai', 'asc');
        }

        $agendas = $query->paginate(10)->withQueryString();

        return view('amil.agenda.index', compact('amil', 'agendas', 'tab'));
    }

    /**
     * Detail agenda penugasan
     */
    public function show(Agenda $agenda)
    {
        $amil = Auth::user()->amil;

        $agenda->load(['amils', 'creator']);

        return view('amil.agenda.show', compact('amil', 'agenda'));
    }
}
