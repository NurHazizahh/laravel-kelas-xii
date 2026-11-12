<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalFilms = Film::count();
        $totalGenres = Genre::count();
        $totalCasts = Cast::count();
        $totalPerans = Peran::count();
        $totalUsers = User::count();
        $totalKritiks = Kritik::count();

        $latestFilms = Film::with('genre')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalFilms',
            'totalGenres',
            'totalCasts',
            'totalPerans',
            'totalUsers',
            'totalKritiks',
            'latestFilms'
        ));
    }
}
