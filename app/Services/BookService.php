<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookService
{
    public function __construct(
        private BookRepositoryInterface $bookRepository
    ) {
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->bookRepository->paginate($perPage);
    }

    public function create(array $data): Book
    {
        return $this->bookRepository->create($data);
    }

    public function show(Book $book): Book
    {
        return $book;
    }

    public function update(Book $book, array $data): Book
    {
        return $this->bookRepository->update($book, $data);
    }

    public function delete(Book $book): void
    {
        $this->bookRepository->delete($book);
    }
}

