<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kritik;
use Illuminate\Http\Request;

class KritikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kritik::with(['user', 'film'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('film', function ($fq) use ($search) {
                      $fq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $kritiks = $query->paginate(15)->withQueryString();

        return view('admin.kritiks.index', compact('kritiks'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kritik $kritik)
    {
        $kritik->delete();

        return redirect()->route('admin.kritiks.index')->with('success', 'Komentar berhasil dihapus.');
    }
}

