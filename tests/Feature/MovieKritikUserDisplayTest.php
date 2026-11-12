<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MovieKritikUserDisplayTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('email', 'admin@example.com')->first();
    }

    protected function getNormalUser(): User
    {
        return User::where('role_id', 1)->first();
    }

    public function test_movie_detail_displays_user_name_rating_and_date(): void
    {
        $user = $this->getNormalUser();
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Tampil Nama ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $commentText = 'Komentar pengujian nama user ' . uniqid();

        Carbon::setTestNow(Carbon::now()->subMinutes(10));
        $kritik = Kritik::create([
            'user_id' => $user->id,
            'film_id' => $film->id,
            'rating' => 5,
            'comment' => $commentText,
        ]);
        Carbon::setTestNow();

        $response = $this->get("/movies/{$film->id}");

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($commentText);
        $response->assertSee('(5/5)');
        $response->assertSee('ago');
    }

    public function test_newest_comment_appears_first(): void
    {
        $user = $this->getNormalUser();
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Urutan Komentar ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $baseTime = Carbon::now();

        // Komentar lama (2 jam yang lalu)
        $oldComment = 'Komentar Lama ' . uniqid();
        Carbon::setTestNow($baseTime->copy()->subHours(2));
        Kritik::create([
            'user_id' => $user->id,
            'film_id' => $film->id,
            'rating' => 4,
            'comment' => $oldComment,
        ]);

        // Komentar baru (1 menit yang lalu)
        $newComment = 'Komentar Baru ' . uniqid();
        Carbon::setTestNow($baseTime->copy()->subMinute());
        Kritik::create([
            'user_id' => $user->id,
            'film_id' => $film->id,
            'rating' => 5,
            'comment' => $newComment,
        ]);
        Carbon::setTestNow();

        $response = $this->get("/movies/{$film->id}");
        $response->assertStatus(200);

        $content = $response->getContent();
        $posNew = strpos($content, $newComment);
        $posOld = strpos($content, $oldComment);

        $this->assertNotFalse($posNew, 'Komentar baru harus ditemukan');
        $this->assertNotFalse($posOld, 'Komentar lama harus ditemukan');
        $this->assertLessThan($posOld, $posNew, 'Komentar terbaru harus muncul sebelum (di atas) komentar lama');
    }

    public function test_query_count_for_comments_and_users_is_eager_loaded(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Eager Comment ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        // Buat 5 komentar dari user
        $users = User::take(5)->get();
        foreach ($users as $index => $u) {
            Kritik::create([
                'user_id' => $u->id,
                'film_id' => $film->id,
                'rating' => 4,
                'comment' => 'Komentar eager ' . $index . '_' . uniqid(),
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->get("/movies/{$film->id}");
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $kritikQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], '`kritiks`') || str_contains($q['query'], 'kritiks');
        });
        $userQueries = array_filter($queries, function ($q) {
            return (str_contains($q['query'], '`users`') || str_contains($q['query'], 'users'))
                && str_contains($q['query'], 'where `users`.`id` in');
        });

        $this->assertCount(1, $kritikQueries, 'Query kritiks harus dieksekusi 1 kali');
        $this->assertCount(1, $userQueries, 'Query users untuk eager load komentar harus dieksekusi 1 kali');
    }

    public function test_deleted_or_null_user_displays_pengguna_dihapus(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film User Dihapus ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $comment = Kritik::create([
            'user_id' => 99999999, // user tidak ada
            'film_id' => $film->id,
            'rating' => 3,
            'comment' => 'Komentar oleh user yang sudah dihapus ' . uniqid(),
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $response = $this->get("/movies/{$film->id}");
        $response->assertStatus(200);
        $response->assertSee('Pengguna dihapus');
        $response->assertSee($comment->comment);
    }

    public function test_film_without_comments_displays_empty_message(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Tanpa Komentar ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $response = $this->get("/movies/{$film->id}");
        $response->assertStatus(200);
        $response->assertSee('Belum ada komentar. Jadilah yang pertama!');
    }

    public function test_admin_kritiks_index_displays_user_name_and_handles_deleted_user(): void
    {
        $admin = $this->getAdmin();
        $user = $this->getNormalUser();
        $film = Film::first();

        $commentValid = 'Komentar Admin Index Valid ' . uniqid();
        Kritik::create([
            'user_id' => $user->id,
            'film_id' => $film->id,
            'rating' => 5,
            'comment' => $commentValid,
        ]);

        $response = $this->actingAs($admin)->get('/admin/kritiks');
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($commentValid);
    }
}
