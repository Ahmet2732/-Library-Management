<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse|AnonymousResourceCollection
    {
        try {
            $books = Book::query()
                ->with('author')
                ->paginate(15);

            return BookResource::collection($books);
        } catch (\Throwable $e) {
            Log::error('Failed to list books', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to fetch books. Please try again later.',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse|BookResource
    {
        try {
            $book = Book::create($request->validated());

            $book->load('author');

            return new BookResource($book);
        } catch (\Throwable $e) {
            Log::error('Failed to create book', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to create book. Please try again later.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): JsonResponse|BookResource
    {
        try {
            $book->load('author');

            return new BookResource($book);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch book', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to fetch book. Please try again later.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): JsonResponse|BookResource
    {
        try {
            $book->update($request->validated());

            $book->load('author');

            return new BookResource($book);
        } catch (\Throwable $e) {
            Log::error('Failed to update book', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to update book. Please try again later.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): JsonResponse
    {
        try {
            $book->delete();

            return response()->json(['message' => 'Book deleted successfully'], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to delete book', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to delete book. Please try again later.',
            ], 500);
        }
    }
}
