<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Task;

interface TaskRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;

    public function find(int $id): ?Task;

    public function create(array $data): Task;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
