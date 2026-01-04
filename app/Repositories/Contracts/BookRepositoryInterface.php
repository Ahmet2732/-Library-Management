<?php

namespace App\Repositories\Contracts;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Book;

    public function update(Book $book, array $data): Book;

    public function delete(Book $book): bool;

    public function find(int $id): ?Book;

    public function findOrFail(int $id): Book;
}

