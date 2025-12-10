<?php

namespace App\Services;

use App\Models\Author;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthorService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Author::query()
        ->paginate($perPage);
    }

    public function create(array $data): Author
    {
        return Author::create($data);
    }

    public function update(Author $author, array $data): Author
    {
        $author->update($data);

        return $author;
    }

    public function delete(Author $author): void
    {
        if ($author->books()->exists()) {
            throw new DomainException('Cannot delete author with existing books.');
        }

        $author->delete();
    }
}

