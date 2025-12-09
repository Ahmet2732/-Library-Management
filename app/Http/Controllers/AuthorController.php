<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse|AnonymousResourceCollection
    {
        try {
            $authors = Author::query()
                ->paginate(15);

            return AuthorResource::collection($authors);
        } catch (\Throwable $e) {
            Log::error('Failed to list authors', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to fetch authors. Please try again later.',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request): JsonResponse|AuthorResource
    {
        try {
            $author = Author::create($request->validated());

            return new AuthorResource($author);
        } catch (\Throwable $e) {
            Log::error('Failed to create author', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to create author. Please try again later.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author): JsonResponse|AuthorResource
    {
        try {
            return new AuthorResource($author);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch author', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to fetch author. Please try again later.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author): JsonResponse|AuthorResource
    {
        try {
            $author->update($request->validated());

            return new AuthorResource($author);
        } catch (\Throwable $e) {
            Log::error('Failed to update author', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to update author. Please try again later.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author): JsonResponse
    {
        try {
            if ($author->books()->exists()) {
                return response()->json([
                    'message' => 'Cannot delete author with existing books.',
                ], 422);
            }

            $author->delete();

            return response()->json(['message' => 'Author deleted successfully'], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to delete author', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to delete author. Please try again later.',
            ], 500);
        }
    }
}
