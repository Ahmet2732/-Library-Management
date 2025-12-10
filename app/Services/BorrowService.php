<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BorrowRecord;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BorrowService
{
    public function borrow(array $data): BorrowRecord
    {
        $book = Book::findOrFail($data['book_id']);

        if ($book->isBorrowed()) {
            throw new DomainException('This book is currently borrowed and cannot be borrowed again until returned.');
        }

        $borrowRecord = BorrowRecord::create([
            'user_name' => $data['user_name'],
            'book_id' => $book->id,
            'borrow_at' => now(),
        ]);

        return $borrowRecord->load('book');
    }

    public function return(int $borrowId): BorrowRecord
    {
        $borrowRecord = BorrowRecord::findOrFail($borrowId);

        if ($borrowRecord->return_at !== null) {
            throw new DomainException('This book has already been returned.');
        }

        $borrowRecord->update([
            'return_at' => now(),
        ]);

        return $borrowRecord->load('book');
    }

    public function history(int $perPage = 15): LengthAwarePaginator
    {
        return BorrowRecord::query()
            ->with('book.author')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}

