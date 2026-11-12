<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use App\Models\User;
use Tests\TestCase;

class AdminDashboard2BTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('email', 'admin@example.com')->first();
    }

    protected function getNormalUser(): User
    {
        return User::where('role_id', 1)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Hak Akses & Middleware Admin
    |--------------------------------------------------------------------------
    */

    public function test_guest_is_redirected_from_all_new_admin_routes(): void
    {
        $routes = [
            '/admin/genres',
            '/admin/genres/create',
            '/admin/casts',
            '/admin/casts/create',
            '/admin/perans',
            '/admin/perans/create',
            '/admin/kritiks',
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertRedirect(route('home'));
        }
    }

    public function test_normal_user_receives_403_from_all_new_admin_routes(): void
    {
        $user = $this->getNormalUser();

        $routes = [
            '/admin/genres',
            '/admin/genres/create',
            '/admin/casts',
            '/admin/casts/create',
            '/admin/perans',
            '/admin/perans/create',
            '/admin/kritiks',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertStatus(403);
        }
    }

    public function test_dashboard_displays_correct_counters_including_peran(): void
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Total Film');
        $response->assertSee('Total Genre');
        $response->assertSee('Total Cast');
        $response->assertSee('Total Peran');
        $response->assertSee('Total Komentar');
        $response->assertSee((string) Peran::count());
    }

    /*
    |--------------------------------------------------------------------------
    | Modul 1: Genre (CRUD)
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_genres_index(): void
    {
        $admin = $this->getAdmin();
        $response = $this->actingAs($admin)->get('/admin/genres');

        $response->assertStatus(200);
        $response->assertSee('Kelola Genre');
    }

    public function test_genre_validation_rules(): void
    {
        $admin = $this->getAdmin();

        // 1. Name required
        $res1 = $this->actingAs($admin)->post('/admin/genres', ['name' => '']);
        $res1->assertSessionHasErrors(['name']);

        // 2. Name max 45 chars
        $res2 = $this->actingAs($admin)->post('/admin/genres', ['name' => str_repeat('A', 46)]);
        $res2->assertSessionHasErrors(['name']);

        // 3. Name unique
        $existing = Genre::first();
        $res3 = $this->actingAs($admin)->post('/admin/genres', ['name' => $existing->name]);
        $res3->assertSessionHasErrors(['name']);
    }

    public function test_admin_can_create_update_and_delete_unused_genre(): void
    {
        $admin = $this->getAdmin();

        // Create
        $uniqueName = 'GenreUnik_' . uniqid();
        $createRes = $this->actingAs($admin)->post('/admin/genres', [
            'name' => $uniqueName,
        ]);
        $createRes->assertRedirect(route('admin.genres.index'));
        $createRes->assertSessionHas('success');

        $genre = Genre::where('name', $uniqueName)->first();
        $this->assertNotNull($genre);

        // Update
        $updatedName = 'GenreUpdate_' . uniqid();
        $updateRes = $this->actingAs($admin)->put("/admin/genres/{$genre->id}", [
            'name' => $updatedName,
        ]);
        $updateRes->assertRedirect(route('admin.genres.index'));
        $updateRes->assertSessionHas('success');

        $genre->refresh();
        $this->assertEquals($updatedName, $genre->name);

        // Delete unused
        $deleteRes = $this->actingAs($admin)->delete("/admin/genres/{$genre->id}");
        $deleteRes->assertRedirect(route('admin.genres.index'));
        $deleteRes->assertSessionHas('success');
        $this->assertNull(Genre::find($genre->id));
    }

    public function test_delete_genre_used_by_films_is_rejected_with_message(): void
    {
        $admin = $this->getAdmin();

        $genre = Genre::create(['name' => 'GenreFilm_' . uniqid()]);
        Film::create([
            'title' => 'Film Pengikat Genre ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $deleteRes = $this->actingAs($admin)->delete("/admin/genres/{$genre->id}");

        $deleteRes->assertRedirect(route('admin.genres.index'));
        $deleteRes->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString('Genre masih dipakai oleh 1 film', $errorMessage);

        // Genre harus masih ada di database
        $this->assertNotNull(Genre::find($genre->id));
    }

    /*
    |--------------------------------------------------------------------------
    | Modul 2: Cast (CRUD)
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_casts_index(): void
    {
        $admin = $this->getAdmin();
        $response = $this->actingAs($admin)->get('/admin/casts');

        $response->assertStatus(200);
        $response->assertSee('Kelola Cast');
    }

    public function test_cast_validation_rules(): void
    {
        $admin = $this->getAdmin();

        // 1. Name required & max 45
        $res1 = $this->actingAs($admin)->post('/admin/casts', [
            'name' => '',
            'age' => 25,
            'bio' => 'Bio',
        ]);
        $res1->assertSessionHasErrors(['name']);

        $res2 = $this->actingAs($admin)->post('/admin/casts', [
            'name' => str_repeat('B', 46),
            'age' => 25,
            'bio' => 'Bio',
        ]);
        $res2->assertSessionHasErrors(['name']);

        // 2. Age integer 1-120
        $res3 = $this->actingAs($admin)->post('/admin/casts', [
            'name' => 'Cast Test',
            'age' => 0,
            'bio' => 'Bio',
        ]);
        $res3->assertSessionHasErrors(['age']);

        $res4 = $this->actingAs($admin)->post('/admin/casts', [
            'name' => 'Cast Test',
            'age' => 125,
            'bio' => 'Bio',
        ]);
        $res4->assertSessionHasErrors(['age']);

        // 3. Bio required
        $res5 = $this->actingAs($admin)->post('/admin/casts', [
            'name' => 'Cast Test',
            'age' => 30,
            'bio' => '',
        ]);
        $res5->assertSessionHasErrors(['bio']);
    }

    public function test_admin_can_create_update_and_delete_unused_cast(): void
    {
        $admin = $this->getAdmin();

        // Create
        $uniqueName = 'CastUnik_' . uniqid();
        $createRes = $this->actingAs($admin)->post('/admin/casts', [
            'name' => $uniqueName,
            'age' => 35,
            'bio' => 'Biografi lengkap aktor pengujian.',
        ]);
        $createRes->assertRedirect(route('admin.casts.index'));
        $createRes->assertSessionHas('success');

        $cast = Cast::where('name', $uniqueName)->first();
        $this->assertNotNull($cast);

        // Update
        $updatedName = 'CastUpd_' . uniqid();
        $updateRes = $this->actingAs($admin)->put("/admin/casts/{$cast->id}", [
            'name' => $updatedName,
            'age' => 40,
            'bio' => 'Biografi telah diperbarui.',
        ]);
        $updateRes->assertRedirect(route('admin.casts.index'));
        $updateRes->assertSessionHas('success');

        $cast->refresh();
        $this->assertEquals($updatedName, $cast->name);
        $this->assertEquals(40, $cast->age);

        // Delete
        $deleteRes = $this->actingAs($admin)->delete("/admin/casts/{$cast->id}");
        $deleteRes->assertRedirect(route('admin.casts.index'));
        $deleteRes->assertSessionHas('success');
        $this->assertNull(Cast::find($cast->id));
    }

    public function test_delete_cast_used_in_peran_is_rejected_without_sql_error(): void
    {
        $admin = $this->getAdmin();
        $film = Film::first();

        $cast = Cast::create([
            'name' => 'CastTerpakai_' . uniqid(),
            'age' => 28,
            'bio' => 'Bio terpakai',
        ]);

        Peran::create([
            'actor' => 'Peran Penting',
            'film_id' => $film->id,
            'cast_id' => $cast->id,
        ]);

        $deleteRes = $this->actingAs($admin)->delete("/admin/casts/{$cast->id}");

        $deleteRes->assertRedirect(route('admin.casts.index'));
        $deleteRes->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString('Cast masih dipakai oleh 1 peran', $errorMessage);

        // Cast tetap ada di database
        $this->assertNotNull(Cast::find($cast->id));
    }

    /*
    |--------------------------------------------------------------------------
    | Modul 3: Peran (CRUD + Eager Loading + Relations)
    |--------------------------------------------------------------------------
    */

    public function test_peran_relations_and_fillable(): void
    {
        $film = Film::first();
        $cast = Cast::first();

        $peran = Peran::create([
            'actor' => 'Relasi Test Actor',
            'film_id' => $film->id,
            'cast_id' => $cast->id,
        ]);

        $this->assertInstanceOf(Film::class, $peran->film);
        $this->assertInstanceOf(Cast::class, $peran->cast);
        $this->assertTrue($film->perans->contains($peran));
        $this->assertTrue($cast->perans->contains($peran));

        $peran->delete();
    }

    public function test_admin_can_view_create_update_and_delete_peran(): void
    {
        $admin = $this->getAdmin();
        $film = Film::first();
        $cast = Cast::first();

        // View index
        $indexRes = $this->actingAs($admin)->get('/admin/perans');
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Kelola Peran');

        // Create
        $actorName = 'Tony Stark ' . uniqid();
        $createRes = $this->actingAs($admin)->post('/admin/perans', [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => $actorName,
        ]);
        $createRes->assertRedirect(route('admin.perans.index'));
        $createRes->assertSessionHas('success');

        $peran = Peran::where('actor', $actorName)->first();
        $this->assertNotNull($peran);

        // Muncul di index admin dengan judul film dan nama cast
        $indexCheck = $this->actingAs($admin)->get('/admin/perans');
        $indexCheck->assertSee($actorName);
        $indexCheck->assertSee($film->title);
        $indexCheck->assertSee($cast->name);

        // Update
        $newActor = 'Iron Man ' . uniqid();
        $updateRes = $this->actingAs($admin)->put("/admin/perans/{$peran->id}", [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => $newActor,
        ]);
        $updateRes->assertRedirect(route('admin.perans.index'));
        $updateRes->assertSessionHas('success');

        $peran->refresh();
        $this->assertEquals($newActor, $peran->actor);

        // Delete
        $deleteRes = $this->actingAs($admin)->delete("/admin/perans/{$peran->id}");
        $deleteRes->assertRedirect(route('admin.perans.index'));
        $deleteRes->assertSessionHas('success');
        $this->assertNull(Peran::find($peran->id));
    }

    /*
    |--------------------------------------------------------------------------
    | Modul 4: Komentar / Kritik (Daftar & Hapus)
    |--------------------------------------------------------------------------
    */

    public function test_kritik_relations_and_display(): void
    {
        $admin = $this->getAdmin();
        $film = Film::first();

        $uniqueComment = 'Komentar Pengujian 2B ' . uniqid();
        $kritik = Kritik::create([
            'user_id' => $admin->id,
            'film_id' => $film->id,
            'rating' => 9,
            'comment' => $uniqueComment,
        ]);

        $this->assertInstanceOf(User::class, $kritik->user);
        $this->assertInstanceOf(Film::class, $kritik->film);

        // Index admin menampilkan nama user dan judul film
        $response = $this->actingAs($admin)->get('/admin/kritiks');
        $response->assertStatus(200);
        $response->assertSee($admin->name);
        $response->assertSee($film->title);
        $response->assertSee($uniqueComment);

        // Delete
        $deleteRes = $this->actingAs($admin)->delete("/admin/kritiks/{$kritik->id}");
        $deleteRes->assertRedirect(route('admin.kritiks.index'));
        $deleteRes->assertSessionHas('success');
        $this->assertNull(Kritik::find($kritik->id));
    }

    public function test_public_pages_remain_functional(): void
    {
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);

        $moviesRes = $this->get('/movies');
        $moviesRes->assertStatus(200);

        $genre = Genre::first();
        if ($genre) {
            $genreRes = $this->get("/movies/genre/{$genre->id}");
            $genreRes->assertStatus(200);
        }
    }
}

