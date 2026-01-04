<?php

namespace App\Repositories;

use App\Models\BorrowRecord;
use App\Repositories\Contracts\BorrowRecordRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BorrowRecordRepository implements BorrowRecordRepositoryInterface
{
    public function create(array $data): BorrowRecord
    {
        return BorrowRecord::create($data);
    }

    public function update(BorrowRecord $borrowRecord, array $data): BorrowRecord
    {
        $borrowRecord->update($data);

        return $borrowRecord->fresh();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return BorrowRecord::query()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?BorrowRecord
    {
        return BorrowRecord::find($id);
    }

    public function findOrFail(int $id): BorrowRecord
    {
        return BorrowRecord::findOrFail($id);
    }
}

