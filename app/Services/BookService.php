<?php

namespace App\Services;

use App\Models\Book;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Book::query()
            ->with('author')
            ->paginate($perPage);
    }

    public function create(array $data): Book
    {
        $book = Book::create($data);
        $book->load('author');

        return $book;
    }

    public function show(Book $book): Book
    {
        return $book->load('author');
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        return $book->load('author');
    }

    public function delete(Book $book): void
    {
        $book->delete();
    }
}

