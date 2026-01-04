<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowBookRequest;
use App\Http\Resources\BorrowRecordResource;
use App\Services\BorrowService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class BorrowController extends Controller
{
    public function __construct(private BorrowService $borrows)
    {
    }

    /**
     * Borrow a book.
     */
    public function borrow(BorrowBookRequest $request): JsonResponse|BorrowRecordResource
    {
        try {
            $borrowRecord = $this->borrows->borrow($request->validated());

            return new BorrowRecordResource($borrowRecord);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Failed to borrow book', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to borrow book. Please try again later.',
            ], 500);
        }
    }

    /**
     * Return a borrowed book.
     */
    public function return(int $borrow_id): JsonResponse|BorrowRecordResource
    {
        try {
            $borrowRecord = $this->borrows->return($borrow_id);

            return new BorrowRecordResource($borrowRecord);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Failed to return book', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to return book. Please try again later.',
            ], 500);
        }
    }

    /**
     * Get borrow history.
     */
    public function history(): JsonResponse|AnonymousResourceCollection
    {
        try {
            return BorrowRecordResource::collection($this->borrows->history());
        } catch (\Throwable $e) {
            Log::error('Failed to fetch borrow history', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to fetch borrow history. Please try again later.',
            ], 500);
        }
    }
}
