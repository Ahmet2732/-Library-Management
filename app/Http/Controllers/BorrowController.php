<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowBookRequest;
use App\Http\Resources\BorrowRecordResource;
use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BorrowController extends Controller
{
    /**
     * Borrow a book.
     */
    public function borrow(BorrowBookRequest $request): JsonResponse|BorrowRecordResource
    {
        $book = Book::findOrFail($request->validated()['book_id']);

        if ($book->isBorrowed()) {
            return response()->json([
                'message' => 'This book is currently borrowed and cannot be borrowed again until returned.',
            ], 422);
        }

        $borrowRecord = BorrowRecord::create([
            'user_name' => $request->validated()['user_name'],
            'book_id' => $book->id,
            'borrow_at' => now(),
        ]);

        $borrowRecord->load('book');

        return new BorrowRecordResource($borrowRecord);
    }

    /**
     * Return a borrowed book.
     */
    public function return(int $borrow_id): JsonResponse|BorrowRecordResource
    {
        $borrowRecord = BorrowRecord::findOrFail($borrow_id);

        if ($borrowRecord->return_at !== null) {
            return response()->json([
                'message' => 'This book has already been returned.',
            ], 422);
        }

        $borrowRecord->update([
            'return_at' => now(),
        ]);

        $borrowRecord->load('book');

        return new BorrowRecordResource($borrowRecord);
    }

    /**
     * Get borrow history.
     */
    public function history(): AnonymousResourceCollection
    {
        $borrowRecords = BorrowRecord::query()
            ->with('book.author')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return BorrowRecordResource::collection($borrowRecords);
    }
}
