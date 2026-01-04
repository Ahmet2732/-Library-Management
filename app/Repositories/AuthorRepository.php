<?php

namespace App\Repositories;

use App\Models\Author;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Author::query()->paginate($perPage);
    }

    public function create(array $data): Author
    {
        return Author::create($data);
    }

    public function update(Author $author, array $data): Author
    {
        $author->update($data);

        return $author->fresh();
    }

    public function delete(Author $author): bool
    {
        return $author->delete();
    }

    public function hasBooks(Author $author): bool
    {
        return $author->books()->exists();
    }

    public function find(int $id): ?Author
    {
        return Author::find($id);
    }

    public function findOrFail(int $id): Author
    {
        return Author::findOrFail($id);
    }
}

