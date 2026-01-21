<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get all tasks with optional filters
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = Task::with('user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('assigned_to', $filters['user_id']);
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Find task by ID
     */
    public function find(int $id): Task
    {
        return Task::with('user')->findOrFail($id);
    }

    /**
     * Create new task
     */
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Update existing task
     */
    public function update(int $id, array $data): bool
    {
        return Task::where('id', $id)->update($data) > 0;
    }

    /**
     * Delete task
     */
    public function delete(int $id): bool
    {
        return Task::destroy($id) > 0;
    }
}
