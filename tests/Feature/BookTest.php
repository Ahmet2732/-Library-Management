<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_book_with_valid_author(): void
    {
        $author = Author::factory()->create();

        $data = [
            'title' => 'The Great Gatsby',
            'description' => 'A classic American novel.',
            'year' => 1925,
            'author_id' => $author->id,
        ];

        $response = $this->postJson('/api/books', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'year',
                    'author_id',
                    'author',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'The Great Gatsby',
                    'description' => 'A classic American novel.',
                    'year' => 1925,
                    'author_id' => $author->id,
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'The Great Gatsby',
            'author_id' => $author->id,
        ]);
    }

    public function test_cannot_create_book_with_invalid_author(): void
    {
        $data = [
            'title' => 'Test Book',
            'description' => 'Test Description',
            'year' => 2020,
            'author_id' => 99999, // Non-existent author
        ];

        $response = $this->postJson('/api/books', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['author_id']);
    }

    public function test_can_list_books_with_pagination(): void
    {
        $author = Author::factory()->create();
        Book::factory()->count(20)->create(['author_id' => $author->id]);

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'year',
                        'author_id',
                        'author',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(15, $response->json('data'));
    }

    public function test_can_get_single_book(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create([
            'title' => '1984',
            'author_id' => $author->id,
        ]);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => '1984',
                    'author_id' => $author->id,
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'author' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    public function test_can_update_book(): void
    {
        $author = Author::factory()->create();
        $newAuthor = Author::factory()->create();
        $book = Book::factory()->create([
            'title' => 'Old Title',
            'author_id' => $author->id,
        ]);

        $response = $this->putJson("/api/books/{$book->id}", [
            'title' => 'New Title',
            'description' => 'Updated description',
            'year' => 2023,
            'author_id' => $newAuthor->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'New Title',
                    'description' => 'Updated description',
                    'year' => 2023,
                    'author_id' => $newAuthor->id,
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'New Title',
            'author_id' => $newAuthor->id,
        ]);
    }

    public function test_can_delete_book(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Book deleted successfully',
            ]);

        $this->assertSoftDeleted('books', [
            'id' => $book->id,
        ]);
    }

    public function test_book_validation_requires_title(): void
    {
        $author = Author::factory()->create();

        $response = $this->postJson('/api/books', [
            'description' => 'Test Description',
            'year' => 2020,
            'author_id' => $author->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_book_validation_requires_valid_year(): void
    {
        $author = Author::factory()->create();

        $response = $this->postJson('/api/books', [
            'title' => 'Test Book',
            'description' => 'Test Description',
            'year' => 3000, // Future year
            'author_id' => $author->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['year']);
    }
}

