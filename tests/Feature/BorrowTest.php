<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_borrow_a_book(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $response = $this->postJson('/api/borrow', [
            'user_name' => 'John Doe',
            'book_id' => $book->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_name',
                    'book_id',
                    'book',
                    'borrow_at',
                    'return_at',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'user_name' => 'John Doe',
                    'book_id' => $book->id,
                    'return_at' => null,
                ],
            ]);

        $this->assertDatabaseHas('borrow_records', [
            'user_name' => 'John Doe',
            'book_id' => $book->id,
            'return_at' => null,
        ]);
    }

    public function test_cannot_borrow_already_borrowed_book(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        // First borrow
        BorrowRecord::create([
            'user_name' => 'First User',
            'book_id' => $book->id,
            'borrow_at' => now(),
        ]);

        // Try to borrow again
        $response = $this->postJson('/api/borrow', [
            'user_name' => 'Second User',
            'book_id' => $book->id,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'This book is currently borrowed and cannot be borrowed again until returned.',
            ]);

        $this->assertDatabaseCount('borrow_records', 1);
    }

    public function test_can_return_a_borrowed_book(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $borrowRecord = BorrowRecord::create([
            'user_name' => 'John Doe',
            'book_id' => $book->id,
            'borrow_at' => now(),
        ]);

        $response = $this->postJson("/api/return/{$borrowRecord->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_name',
                    'book_id',
                    'borrow_at',
                    'return_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => $borrowRecord->id,
                ],
            ]);

        $this->assertNotNull($response->json('data.return_at'));

        $this->assertDatabaseHas('borrow_records', [
            'id' => $borrowRecord->id,
        ]);

        $borrowRecord->refresh();
        $this->assertNotNull($borrowRecord->return_at);
    }

    public function test_can_borrow_book_after_return(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        // First borrow
        $firstBorrow = BorrowRecord::create([
            'user_name' => 'First User',
            'book_id' => $book->id,
            'borrow_at' => now(),
        ]);

        // Return the book
        $this->postJson("/api/return/{$firstBorrow->id}");

        // Borrow again
        $response = $this->postJson('/api/borrow', [
            'user_name' => 'Second User',
            'book_id' => $book->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'user_name' => 'Second User',
                    'book_id' => $book->id,
                ],
            ]);

        $this->assertDatabaseCount('borrow_records', 2);
    }

    public function test_cannot_return_already_returned_book(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $borrowRecord = BorrowRecord::create([
            'user_name' => 'John Doe',
            'book_id' => $book->id,
            'borrow_at' => now(),
            'return_at' => now(),
        ]);

        $response = $this->postJson("/api/return/{$borrowRecord->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'This book has already been returned.',
            ]);
    }

    public function test_can_get_borrow_history(): void
    {
        $author = Author::factory()->create();
        $book1 = Book::factory()->create(['author_id' => $author->id]);
        $book2 = Book::factory()->create(['author_id' => $author->id]);

        BorrowRecord::create([
            'user_name' => 'User 1',
            'book_id' => $book1->id,
            'borrow_at' => now()->subDays(5),
            'return_at' => now()->subDays(2),
        ]);

        BorrowRecord::create([
            'user_name' => 'User 2',
            'book_id' => $book2->id,
            'borrow_at' => now()->subDays(1),
        ]);

        $response = $this->getJson('/api/borrow/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_name',
                        'book_id',
                        'book',
                        'borrow_at',
                        'return_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_borrow_validation_requires_user_name(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $response = $this->postJson('/api/borrow', [
            'book_id' => $book->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_name']);
    }

    public function test_borrow_validation_requires_valid_book_id(): void
    {
        $response = $this->postJson('/api/borrow', [
            'user_name' => 'John Doe',
            'book_id' => 99999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_id']);
    }
}

