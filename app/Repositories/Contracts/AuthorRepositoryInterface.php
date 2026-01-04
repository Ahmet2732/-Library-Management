<?php

namespace App\Repositories\Contracts;

use App\Models\Author;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AuthorRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Author;

    public function update(Author $author, array $data): Author;

    public function delete(Author $author): bool;

    public function hasBooks(Author $author): bool;

    public function find(int $id): ?Author;

    public function findOrFail(int $id): Author;
}

