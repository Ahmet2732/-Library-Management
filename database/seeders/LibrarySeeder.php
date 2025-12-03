<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Authors
        $rowling = Author::create([
            'name' => 'J.K. Rowling',
            'bio' => 'British author, best known for the Harry Potter series. Born in Yate, Gloucestershire, England.',
            'dob' => '1965-07-31',
        ]);

        $orwell = Author::create([
            'name' => 'George Orwell',
            'bio' => 'English novelist, essayist, journalist and critic. Best known for the dystopian novel 1984 and the allegorical novella Animal Farm.',
            'dob' => '1903-06-25',
        ]);

        $fitzgerald = Author::create([
            'name' => 'F. Scott Fitzgerald',
            'bio' => 'American novelist, essayist, and short story writer. Best known for his novel The Great Gatsby.',
            'dob' => '1896-09-24',
        ]);

        $tolkien = Author::create([
            'name' => 'J.R.R. Tolkien',
            'bio' => 'English writer, poet, philologist, and academic, best known as the author of the high fantasy works The Hobbit and The Lord of the Rings.',
            'dob' => '1892-01-03',
        ]);

        $salinger = Author::create([
            'name' => 'J.D. Salinger',
            'bio' => 'American writer known for his novel The Catcher in the Rye.',
            'dob' => '1919-01-01',
        ]);

        // Create Books
        $book1 = Book::create([
            'title' => 'Harry Potter and the Philosopher\'s Stone',
            'description' => 'The first book in the Harry Potter series, following the adventures of a young wizard.',
            'year' => 1997,
            'author_id' => $rowling->id,
        ]);

        $book2 = Book::create([
            'title' => '1984',
            'description' => 'A dystopian social science fiction novel and cautionary tale about the dangers of totalitarianism.',
            'year' => 1949,
            'author_id' => $orwell->id,
        ]);

        $book3 = Book::create([
            'title' => 'Animal Farm',
            'description' => 'An allegorical novella that reflects events leading up to the Russian Revolution of 1917.',
            'year' => 1945,
            'author_id' => $orwell->id,
        ]);

        $book4 = Book::create([
            'title' => 'The Great Gatsby',
            'description' => 'A classic American novel set in the Jazz Age, exploring themes of decadence and excess.',
            'year' => 1925,
            'author_id' => $fitzgerald->id,
        ]);

        $book5 = Book::create([
            'title' => 'The Hobbit',
            'description' => 'A fantasy novel about the adventures of Bilbo Baggins, a hobbit who goes on an unexpected journey.',
            'year' => 1937,
            'author_id' => $tolkien->id,
        ]);

        $book6 = Book::create([
            'title' => 'The Lord of the Rings: The Fellowship of the Ring',
            'description' => 'The first volume of The Lord of the Rings, following Frodo Baggins on his quest to destroy the One Ring.',
            'year' => 1954,
            'author_id' => $tolkien->id,
        ]);

        $book7 = Book::create([
            'title' => 'The Catcher in the Rye',
            'description' => 'A controversial novel about teenage rebellion and alienation in post-World War II America.',
            'year' => 1951,
            'author_id' => $salinger->id,
        ]);

        $book8 = Book::create([
            'title' => 'Harry Potter and the Chamber of Secrets',
            'description' => 'The second book in the Harry Potter series, where Harry returns to Hogwarts for his second year.',
            'year' => 1998,
            'author_id' => $rowling->id,
        ]);

        // Create Borrow Records
        BorrowRecord::create([
            'user_name' => 'John Doe',
            'book_id' => $book1->id,
            'borrow_at' => now()->subDays(10),
            'return_at' => now()->subDays(5),
        ]);

        BorrowRecord::create([
            'user_name' => 'Jane Smith',
            'book_id' => $book2->id,
            'borrow_at' => now()->subDays(3),
            'return_at' => null, // Currently borrowed
        ]);

        BorrowRecord::create([
            'user_name' => 'Bob Johnson',
            'book_id' => $book4->id,
            'borrow_at' => now()->subDays(7),
            'return_at' => now()->subDays(2),
        ]);

        BorrowRecord::create([
            'user_name' => 'Alice Williams',
            'book_id' => $book5->id,
            'borrow_at' => now()->subDays(1),
            'return_at' => null, // Currently borrowed
        ]);

        $this->command->info('Library data seeded successfully!');
        $this->command->info('Created: 5 authors, 8 books, 4 borrow records');
    }
}
