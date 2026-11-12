<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Peran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MovieDetailCastTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('email', 'admin@example.com')->first();
    }

    public function test_movie_detail_displays_cast_name_and_headers(): void
    {
        $genre = Genre::first();
        $cast = Cast::create([
            'name' => 'Aktor Uji ' . uniqid(),
            'age' => 45,
            'bio' => 'Biografi aktor uji',
        ]);

        $film = Film::create([
            'title' => 'Film Detail Test ' . uniqid(),
            'sinopsis' => 'Sinopsis film detail test.',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $peran = Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Karakter Utama ' . uniqid(),
        ]);

        $response = $this->get("/movies/{$film->id}");

        $response->assertStatus(200);
        $response->assertSee('Nama Cast');
        $response->assertSee('Peran');
        $response->assertSee($cast->name);
        $response->assertSee($peran->actor);
    }

    public function test_movie_detail_displays_empty_message_when_no_cast(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Tanpa Peran ' . uniqid(),
            'sinopsis' => 'Film ini belum memiliki peran sama sekali.',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $response = $this->get("/movies/{$film->id}");

        $response->assertStatus(200);
        $response->assertSee('Belum ada data cast');
    }

    public function test_movie_detail_handles_null_cast_relation_gracefully(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Null Cast ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $peran = Peran::create([
            'actor' => 'Karakter Tanpa Aktor ' . uniqid(),
            'film_id' => $film->id,
            'cast_id' => 9999999,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $response = $this->get("/movies/{$film->id}");
        $response->assertStatus(200);
        $response->assertSee('-');
        $response->assertSee($peran->actor);
    }

    public function test_query_count_for_perans_and_casts_is_eager_loaded(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Eager Test ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        // Buat 5 peran berbeda untuk film ini
        for ($i = 0; $i < 5; $i++) {
            $cast = Cast::create([
                'name' => 'Cast Eager ' . $i . '_' . uniqid(),
                'age' => 30 + $i,
                'bio' => 'Bio',
            ]);

            Peran::create([
                'film_id' => $film->id,
                'cast_id' => $cast->id,
                'actor' => 'Role ' . $i,
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->get("/movies/{$film->id}");
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Cari query yang menyeleksi tabel perans dan casts
        $peranQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], '`perans`') || str_contains($q['query'], 'perans');
        });
        $castQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], '`casts`') || str_contains($q['query'], 'casts');
        });

        // Eager loading harus mengeksekusi tepat 1 query untuk perans dan 1 query untuk casts (bukan per baris peran)
        $this->assertCount(1, $peranQueries, 'Query perans harus dieksekusi 1 kali');
        $this->assertCount(1, $castQueries, 'Query casts harus dieksekusi 1 kali (eager loaded)');
    }

    public function test_peran_added_via_admin_appears_on_public_movie_detail(): void
    {
        $admin = $this->getAdmin();
        $film = Film::first();
        $cast = Cast::create([
            'name' => 'Aktor Dari Admin ' . uniqid(),
            'age' => 33,
            'bio' => 'Bio aktor admin',
        ]);

        $actorRole = 'Karakter Dari Admin ' . uniqid();

        // Tambah peran via admin route (Tugas 2B)
        $storeRes = $this->actingAs($admin)->post('/admin/perans', [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => $actorRole,
        ]);
        $storeRes->assertRedirect(route('admin.perans.index'));

        // Akses halaman publik detail film sebagai guest
        $detailRes = $this->get("/movies/{$film->id}");
        $detailRes->assertStatus(200);
        $detailRes->assertSee($cast->name);
        $detailRes->assertSee($actorRole);
    }
}
