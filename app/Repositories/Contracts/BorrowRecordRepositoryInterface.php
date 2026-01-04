<?php

namespace App\Repositories\Contracts;

use App\Models\BorrowRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BorrowRecordRepositoryInterface
{
    public function create(array $data): BorrowRecord;

    public function update(BorrowRecord $borrowRecord, array $data): BorrowRecord;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?BorrowRecord;

    public function findOrFail(int $id): BorrowRecord;
}

