<?php

namespace App\Services;

use App\Jobs\ProcessAITask;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Task;

class TaskService
{
    protected TaskRepositoryInterface $repo;

    public function __construct(
        TaskRepositoryInterface $repo
    ) {
        $this->repo = $repo;
    }

    /**
     * Store task and queue AI processing
     */
    public function store(array $data): Task
    {
        return DB::transaction(function () use ($data) {

            // Create task
            $task = $this->repo->create($data);

            // Dispatch AI processing to queue
            ProcessAITask::dispatch($task);

            // Return task (AI data will be populated after job processes)
            return $task;
        });
    }
}
