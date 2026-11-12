<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use App\Models\Cast;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminFilmTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('email', 'admin@example.com')->first();
    }

    protected function getNormalUser(): User
    {
        return User::where('role_id', 1)->first();
    }

    protected function createFakePng(string $name = 'poster.png', int $extraKilobytes = 0): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        if ($extraKilobytes > 0) {
            $png .= str_repeat('A', $extraKilobytes * 1024);
        }
        return UploadedFile::fake()->createWithContent($name, $png);
    }

    public function test_guest_cannot_access_admin_dashboard_and_is_redirected(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_normal_user_receives_403_when_accessing_admin(): void
    {
        $user = $this->getNormalUser();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_with_correct_metrics(): void
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
        $response->assertSee(Film::count());
        $response->assertSee(Genre::count());
        $response->assertSee(User::count());
    }

    public function test_admin_can_create_film_with_poster_and_it_appears_on_admin_and_public(): void
    {
        Storage::fake('public');

        $admin = $this->getAdmin();
        $genre = Genre::first();
        $posterFile = $this->createFakePng('avatar_film.png');

        $response = $this->actingAs($admin)->post('/admin/films', [
            'title' => 'Film Uji Coba Baru',
            'sinopsis' => 'Sinopsis film uji coba untuk pengetesan sistem admin.',
            'year' => 2025,
            'genre_id' => $genre->id,
            'poster' => $posterFile,
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        $film = Film::where('title', 'Film Uji Coba Baru')->first();
        $this->assertNotNull($film);
        $this->assertTrue(str_starts_with($film->poster, 'posters/') || str_starts_with($film->poster, 'storage/images/'));

        $relativePath = preg_replace('#^storage/#', '', $film->poster);
        Storage::disk('public')->assertExists($relativePath);

        // Verifikasi tampil di index admin
        $adminIndex = $this->actingAs($admin)->get('/admin/films');
        $adminIndex->assertStatus(200);
        $adminIndex->assertSee('Film Uji Coba Baru');

        // Verifikasi tampil di halaman publik /movies
        $publicMovies = $this->get('/movies');
        $publicMovies->assertStatus(200);
        $publicMovies->assertSee('Film Uji Coba Baru');
    }

    public function test_admin_can_edit_film_without_changing_poster(): void
    {
        $admin = $this->getAdmin();
        $film = Film::where('title', 'Film Uji Coba Baru')->first() ?? Film::first();
        $oldPoster = $film->poster;

        $response = $this->actingAs($admin)->put("/admin/films/{$film->id}", [
            'title' => 'Judul Film Diperbarui',
            'sinopsis' => 'Sinopsis diperbarui tanpa ubah poster.',
            'year' => 2026,
            'genre_id' => $film->genre_id,
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        $film->refresh();
        $this->assertEquals('Judul Film Diperbarui', $film->title);
        $this->assertEquals($oldPoster, $film->poster);
    }

    public function test_admin_can_edit_film_with_new_poster(): void
    {
        Storage::fake('public');

        $admin = $this->getAdmin();
        $film = Film::first();
        $newPoster = $this->createFakePng('poster_baru.png');

        $response = $this->actingAs($admin)->put("/admin/films/{$film->id}", [
            'title' => $film->title,
            'sinopsis' => $film->sinopsis,
            'year' => $film->year,
            'genre_id' => $film->genre_id,
            'poster' => $newPoster,
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $film->refresh();

        $this->assertTrue(str_starts_with($film->poster, 'posters/') || str_starts_with($film->poster, 'storage/images/'));
        $relativePath = preg_replace('#^storage/#', '', $film->poster);
        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_film_validation_rules(): void
    {
        $admin = $this->getAdmin();

        // 1. Judul kosong
        $response1 = $this->actingAs($admin)->post('/admin/films', [
            'title' => '',
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'genre_id' => 1,
            'poster' => $this->createFakePng('test.png'),
        ]);
        $response1->assertSessionHasErrors(['title']);

        // 2. Upload bukan gambar (misal file .txt atau .pdf)
        $response2 = $this->actingAs($admin)->post('/admin/films', [
            'title' => 'Judul Film',
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'genre_id' => 1,
            'poster' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ]);
        $response2->assertSessionHasErrors(['poster']);

        // 3. Ukuran melebihi 2048 KB (misal 3000 KB)
        $response3 = $this->actingAs($admin)->post('/admin/films', [
            'title' => 'Judul Film',
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'genre_id' => 1,
            'poster' => $this->createFakePng('oversize.png', 3000),
        ]);
        $response3->assertSessionHasErrors(['poster']);
    }

    public function test_admin_can_delete_film_with_peran_and_kritik_without_fk_error(): void
    {
        $admin = $this->getAdmin();
        $genre = Genre::first();
        $cast = Cast::first() ?? Cast::create(['name' => 'Aktor Uji', 'age' => 30, 'bio' => '-']);

        $film = Film::create([
            'title' => 'Film Yang Akan Dihapus',
            'sinopsis' => 'Sinopsis film hapus.',
            'year' => 2023,
            'poster' => 'storage/images/sample_to_delete.jpg',
            'genre_id' => $genre->id,
        ]);

        Peran::create([
            'actor' => 'Karakter Pahlawan',
            'film_id' => $film->id,
            'cast_id' => $cast->id,
        ]);

        Kritik::create([
            'comment' => 'Komentar pengujian untuk film ini.',
            'rating' => 5,
            'user_id' => $admin->id,
            'film_id' => $film->id,
        ]);

        $this->assertEquals(1, Peran::where('film_id', $film->id)->count());
        $this->assertEquals(1, Kritik::where('film_id', $film->id)->count());

        $response = $this->actingAs($admin)->delete("/admin/films/{$film->id}");

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        $this->assertNull(Film::find($film->id));
        $this->assertEquals(0, Peran::where('film_id', $film->id)->count());
        $this->assertEquals(0, Kritik::where('film_id', $film->id)->count());

        // Film hilang dari halaman publik
        $public = $this->get('/movies');
        $public->assertDontSee('Film Yang Akan Dihapus');
    }

    public function test_search_and_pagination_functions_properly(): void
    {
        $admin = $this->getAdmin();

        $genre = Genre::first();
        $uniqueTitle = 'UniqueSpecialMovieKeyword999';

        Film::create([
            'title' => $uniqueTitle,
            'sinopsis' => 'Sinopsis khusus unik.',
            'year' => 2025,
            'poster' => 'storage/images/c10.jpg',
            'genre_id' => $genre->id,
        ]);

        // Search dengan keyword
        $searchResponse = $this->actingAs($admin)->get("/admin/films?search={$uniqueTitle}");
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee($uniqueTitle);

        // Index default pagination 10 item per page
        $indexResponse = $this->actingAs($admin)->get('/admin/films');
        $indexResponse->assertStatus(200);
        $filmsOnPage = $indexResponse->viewData('films');
        $this->assertCount(10, $filmsOnPage);
    }
}
