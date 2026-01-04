<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookRepository implements BookRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Book::query()->paginate($perPage);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        return $book->delete();
    }

    public function find(int $id): ?Book
    {
        return Book::find($id);
    }

    public function findOrFail(int $id): Book
    {
        return Book::findOrFail($id);
    }
}

