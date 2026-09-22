<?php

namespace Tests\Feature;

use App\Models\Guru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_gurus(): void
    {
        Guru::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
            'password' => 'password123',
            'foto' => 'budi.jpg',
            'keahlian' => 'Teknik Informatika',
        ]);

        $response = $this->getJson('/api/guru');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data guru berhasil diambil!',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'nama',
                        'nik',
                        'email',
                        'no_hp',
                        'password',
                        'foto',
                        'keahlian',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_can_create_guru(): void
    {
        $payload = [
            'nama' => 'Siti Aminah',
            'nik' => '9876543210',
            'email' => 'siti@example.com',
            'no_hp' => '089876543210',
            'password' => 'secret123',
            'foto' => 'siti.jpg',
            'keahlian' => 'Akuntansi',
        ];

        $response = $this->postJson('/api/guru', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Data guru berhasil ditambahkan!',
                'data' => [
                    'nama' => 'Siti Aminah',
                    'nik' => '9876543210',
                    'email' => 'siti@example.com',
                    'no_hp' => '089876543210',
                    'foto' => 'siti.jpg',
                    'keahlian' => 'Akuntansi',
                ],
            ]);

        $this->assertDatabaseHas('gurus', [
            'nik' => '9876543210',
            'email' => 'siti@example.com',
            'keahlian' => 'Akuntansi',
        ]);
    }

    public function test_can_show_guru(): void
    {
        $guru = Guru::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
            'password' => 'password123',
            'foto' => 'budi.jpg',
            'keahlian' => 'Teknik Informatika',
        ]);

        $response = $this->getJson("/api/guru/{$guru->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data guru berhasil diambil!',
                'data' => [
                    'id' => $guru->id,
                    'nama' => 'Budi Santoso',
                    'nik' => '1234567890',
                ],
            ]);
    }

    public function test_show_guru_not_found(): void
    {
        $response = $this->getJson('/api/guru/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Data guru tidak ditemukan!',
            ]);
    }

    public function test_can_update_guru(): void
    {
        $guru = Guru::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
            'password' => 'password123',
            'foto' => 'budi.jpg',
            'keahlian' => 'Teknik Informatika',
        ]);

        $payload = [
            'nama' => 'Budi Santoso Updated',
            'nik' => '1234567890',
            'email' => 'budi.new@example.com',
            'no_hp' => '081234567899',
            'password' => 'newpassword123',
            'foto' => 'budi_new.jpg',
            'keahlian' => 'Desain Grafis',
        ];

        $response = $this->putJson("/api/guru/{$guru->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data guru berhasil diupdate!',
                'data' => [
                    'nama' => 'Budi Santoso Updated',
                    'email' => 'budi.new@example.com',
                    'keahlian' => 'Desain Grafis',
                ],
            ]);

        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'nama' => 'Budi Santoso Updated',
            'email' => 'budi.new@example.com',
            'keahlian' => 'Desain Grafis',
        ]);
    }

    public function test_can_delete_guru(): void
    {
        $guru = Guru::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
            'password' => 'password123',
            'foto' => 'budi.jpg',
            'keahlian' => 'Teknik Informatika',
        ]);

        $response = $this->deleteJson("/api/guru/{$guru->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data guru berhasil dihapus!',
            ]);

        $this->assertDatabaseMissing('gurus', [
            'id' => $guru->id,
        ]);
    }

    public function test_create_guru_validation_errors(): void
    {
        $response = $this->postJson('/api/guru', [
            'nama' => '',
            'keahlian' => 'Keahlian Tidak Valid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama', 'nik', 'email', 'no_hp', 'password', 'keahlian']);
    }

    public function test_can_partial_update_guru_via_patch(): void
    {
        $guru = Guru::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
            'password' => 'password123',
            'foto' => 'budi.jpg',
            'keahlian' => 'Teknik Informatika',
        ]);

        $response = $this->patchJson("/api/guru/{$guru->id}", [
            'nama' => 'Budi Patch',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data guru berhasil diupdate!',
                'data' => [
                    'id' => $guru->id,
                    'nama' => 'Budi Patch',
                    'nik' => '1234567890',
                    'keahlian' => 'Teknik Informatika',
                ],
            ]);

        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'nama' => 'Budi Patch',
        ]);
    }

    public function test_update_guru_not_found(): void
    {
        $response = $this->putJson('/api/guru/999', [
            'nama' => 'Non Existent',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Data guru tidak ditemukan!',
            ]);
    }

    public function test_delete_guru_not_found(): void
    {
        $response = $this->deleteJson('/api/guru/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Data guru tidak ditemukan!',
            ]);
    }
}
