<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\User;
use Tests\TestCase;

class MovieKritikAuthTest extends TestCase
{
    protected function getNormalUser(): User
    {
        return User::where('role_id', 1)->first();
    }

    protected function getAnotherUser(): User
    {
        return User::where('role_id', 1)->skip(1)->first() ?? User::factory()->create(['role_id' => 1]);
    }

    public function test_guest_cannot_see_comment_form_and_sees_login_prompt(): void
    {
        $film = Film::first();

        $response = $this->get("/movies/{$film->id}");

        $response->assertStatus(200);
        $response->assertSee('Silakan login terlebih dahulu untuk menulis komentar');
        $response->assertSee('data-target="#myModal"', false);
        $response->assertDontSee('Berkomentar sebagai');
        $response->assertDontSee(route('kritik.store', $film));
    }

    public function test_guest_post_to_kritik_store_is_blocked_and_redirects(): void
    {
        $film = Film::first();
        $initialKritikCount = Kritik::count();

        $response = $this->post("/movies/{$film->id}/kritik", [
            'rating' => 5,
            'comment' => 'Komentar dari tamu tanpa login',
        ]);

        // Auth middleware redirects unauthenticated users
        $response->assertRedirect();
        $this->assertGuest();
        $this->assertEquals($initialKritikCount, Kritik::count());
    }

    public function test_authenticated_user_sees_form_with_name_and_can_submit_valid_comment(): void
    {
        $user = $this->getNormalUser();
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Kritik Valid ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $pageResponse = $this->actingAs($user)->get("/movies/{$film->id}");
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee("Berkomentar sebagai");
        $pageResponse->assertSee($user->name);
        $pageResponse->assertSee(route('kritik.store', $film));

        // Submit comment
        $commentText = 'Film ini sangat luar biasa dan memukau! ' . uniqid();
        $postResponse = $this->actingAs($user)->post("/movies/{$film->id}/kritik", [
            'rating' => 5,
            'comment' => $commentText,
        ]);

        $postResponse->assertRedirect(route('movies.show', $film->id) . '#komentar');
        $postResponse->assertSessionHas('success', 'Komentar berhasil dikirim');

        // Tersimpan di database dengan user_id dan film_id yang benar
        $kritik = Kritik::where('comment', $commentText)->first();
        $this->assertNotNull($kritik);
        $this->assertEquals($user->id, $kritik->user_id);
        $this->assertEquals($film->id, $kritik->film_id);
        $this->assertEquals(5, $kritik->rating);

        // Muncul di halaman film tersebut
        $detailResponse = $this->get("/movies/{$film->id}");
        $detailResponse->assertSee($commentText);
    }

    public function test_comment_validation_rules_fail_and_do_not_persist_data(): void
    {
        $user = $this->getNormalUser();
        $film = Film::first();
        $initialCount = Kritik::count();

        // 1. Rating kosong
        $res1 = $this->actingAs($user)->post("/movies/{$film->id}/kritik", [
            'rating' => '',
            'comment' => 'Komentar valid di atas 3 karakter.',
        ]);
        $res1->assertSessionHasErrors(['rating']);

        // 2. Rating di luar range 1-5 (misal 6 atau 0)
        $res2 = $this->actingAs($user)->post("/movies/{$film->id}/kritik", [
            'rating' => 6,
            'comment' => 'Komentar valid di atas 3 karakter.',
        ]);
        $res2->assertSessionHasErrors(['rating']);

        $res3 = $this->actingAs($user)->post("/movies/{$film->id}/kritik", [
            'rating' => 0,
            'comment' => 'Komentar valid di atas 3 karakter.',
        ]);
        $res3->assertSessionHasErrors(['rating']);

        // 3. Komentar kosong
        $res4 = $this->actingAs($user)->post("/movies/{$film->id}/kritik", [
            'rating' => 4,
            'comment' => '',
        ]);
        $res4->assertSessionHasErrors(['comment']);

        // 4. Komentar kurang dari 3 karakter
        $res5 = $this->actingAs($user)->post("/movies/{$film->id}/kritik", [
            'rating' => 4,
            'comment' => 'Ok',
        ]);
        $res5->assertSessionHasErrors(['comment']);

        $this->assertEquals($initialCount, Kritik::count());
    }

    public function test_user_id_injection_attempt_is_ignored(): void
    {
        $user = $this->getNormalUser();
        $fakeUserId = 99999;
        $film = Film::first();

        $commentText = 'Komentar dengan percobaan injeksi user_id palsu ' . uniqid();

        $response = $this->actingAs($user)->post("/movies/{$film->id}/kritik", [
            'rating' => 4,
            'comment' => $commentText,
            'user_id' => $fakeUserId,
            'film_id' => 99999,
        ]);

        $response->assertRedirect(route('movies.show', $film->id) . '#komentar');

        $kritik = Kritik::where('comment', $commentText)->first();
        $this->assertNotNull($kritik);
        // user_id tetap akun yang sedang login, bukan yang diinjeksi
        $this->assertEquals($user->id, $kritik->user_id);
        $this->assertNotEquals($fakeUserId, $kritik->user_id);
        $this->assertEquals($film->id, $kritik->film_id);
    }

    public function test_comment_on_film_a_does_not_appear_on_film_b(): void
    {
        $user = $this->getNormalUser();
        $genre = Genre::first();

        $filmA = Film::create([
            'title' => 'Film A ' . uniqid(),
            'sinopsis' => 'Sinopsis A',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $filmB = Film::create([
            'title' => 'Film B ' . uniqid(),
            'sinopsis' => 'Sinopsis B',
            'year' => 2024,
            'poster' => 'storage/images/sample.jpg',
            'genre_id' => $genre->id,
        ]);

        $uniqueCommentFilmA = 'Komentar Eksklusif Film A ' . uniqid();

        $this->actingAs($user)->post("/movies/{$filmA->id}/kritik", [
            'rating' => 5,
            'comment' => $uniqueCommentFilmA,
        ]);

        // Muncul di Film A
        $resA = $this->get("/movies/{$filmA->id}");
        $resA->assertSee($uniqueCommentFilmA);

        // Tidak muncul di Film B
        $resB = $this->get("/movies/{$filmB->id}");
        $resB->assertDontSee($uniqueCommentFilmA);
    }

    public function test_modal_login_from_movie_page_returns_to_same_movie_page(): void
    {
        $user = $this->getNormalUser();
        $film = Film::first();
        $movieUrl = route('movies.show', $film->id);

        // Simulasi request login dari halaman detail film (HTTP Referer = $movieUrl)
        $response = $this->from($movieUrl)->post('/login', [
            'email' => $user->email,
            'password' => '12345678',
            'remember' => 1,
            '_form' => 'login',
        ]);

        $response->assertRedirect($movieUrl);
        $this->assertAuthenticated();

        // Kunjungi kembali halaman film, pastikan sudah dikenali sebagai user login
        $moviePage = $this->actingAs($user)->get($movieUrl);
        $moviePage->assertStatus(200);
        $moviePage->assertSee("Berkomentar sebagai");
        $moviePage->assertSee($user->name);
    }
}

