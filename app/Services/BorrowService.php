<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Repositories\Contracts\BorrowRecordRepositoryInterface;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BorrowService
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private BorrowRecordRepositoryInterface $borrowRecordRepository
    ) {
    }

    public function borrow(array $data): BorrowRecord
    {
        $book = $this->bookRepository->findOrFail($data['book_id']);

        if ($book->isBorrowed()) {
            throw new DomainException('This book is currently borrowed and cannot be borrowed again until returned.');
        }

        return $this->borrowRecordRepository->create([
            'user_name' => $data['user_name'],
            'book_id' => $book->id,
            'borrow_at' => now(),
        ]);
    }

    public function return(int $borrowId): BorrowRecord
    {
        $borrowRecord = $this->borrowRecordRepository->findOrFail($borrowId);

        if ($borrowRecord->return_at !== null) {
            throw new DomainException('This book has already been returned.');
        }

        return $this->borrowRecordRepository->update($borrowRecord, [
            'return_at' => now(),
        ]);
    }

    public function history(int $perPage = 15): LengthAwarePaginator
    {
        return $this->borrowRecordRepository->paginate($perPage);
    }
}

