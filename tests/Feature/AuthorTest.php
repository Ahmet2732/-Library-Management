<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_author(): void
    {
        $data = [
            'name' => 'J.K. Rowling',
            'bio' => 'British author, best known for the Harry Potter series.',
            'dob' => '1965-07-31',
        ];

        $response = $this->postJson('/api/authors', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'bio',
                    'dob',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'name' => 'J.K. Rowling',
                    'bio' => 'British author, best known for the Harry Potter series.',
                    'dob' => '1965-07-31',
                ],
            ]);

        $this->assertDatabaseHas('authors', [
            'name' => 'J.K. Rowling',
            'bio' => 'British author, best known for the Harry Potter series.',
        ]);
    }

    public function test_can_list_authors_with_pagination(): void
    {
        Author::factory()->count(20)->create();

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'bio',
                        'dob',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(15, $response->json('data'));
    }

    public function test_can_get_single_author(): void
    {
        $author = Author::factory()->create([
            'name' => 'George Orwell',
            'dob' => '1903-06-25',
        ]);

        $response = $this->getJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $author->id,
                    'name' => 'George Orwell',
                    'dob' => '1903-06-25',
                ],
            ]);
    }

    public function test_can_update_author(): void
    {
        $author = Author::factory()->create([
            'name' => 'Old Name',
        ]);

        $response = $this->putJson("/api/authors/{$author->id}", [
            'name' => 'New Name',
            'bio' => 'Updated biography',
            'dob' => '1990-01-01',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'New Name',
                    'bio' => 'Updated biography',
                ],
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_author_without_books(): void
    {
        $author = Author::factory()->create();

        $response = $this->deleteJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Author deleted successfully',
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => $author->id,
        ]);
    }

    public function test_cannot_delete_author_with_books(): void
    {
        $author = Author::factory()->create();
        $author->books()->create([
            'title' => 'Test Book',
            'description' => 'Test Description',
            'year' => 2020,
        ]);

        $response = $this->deleteJson("/api/authors/{$author->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot delete author with existing books.',
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
        ]);
    }

    public function test_author_validation_requires_name(): void
    {
        $response = $this->postJson('/api/authors', [
            'bio' => 'Some bio',
            'dob' => '1990-01-01',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_author_validation_requires_dob(): void
    {
        $response = $this->postJson('/api/authors', [
            'name' => 'Test Author',
            'bio' => 'Some bio',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['dob']);
    }
}

