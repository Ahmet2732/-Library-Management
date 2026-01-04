<?php

namespace App\Services;

use App\Models\Author;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthorService
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository
    ) {
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->authorRepository->paginate($perPage);
    }

    public function create(array $data): Author
    {
        return $this->authorRepository->create($data);
    }

    public function update(Author $author, array $data): Author
    {
        return $this->authorRepository->update($author, $data);
    }

    public function delete(Author $author): void
    {
        if ($this->authorRepository->hasBooks($author)) {
            throw new DomainException('Cannot delete author with existing books.');
        }

        $this->authorRepository->delete($author);
    }
}

