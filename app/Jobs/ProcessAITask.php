<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\AIService;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAITask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Task $task
    ) {
        $this->onQueue('ai');
    }

    /**
     * Execute the job.
     */
    public function handle(AIService $aiService, TaskRepositoryInterface $repo): void
    {
        try {
            Log::info('Processing AI task', ['task_id' => $this->task->id]);

            // Generate AI summary & priority
            $aiData = $aiService->generateSummary($this->task);

            // Update task with AI results
            $repo->update($this->task->id, $aiData);

            Log::info('AI task processed successfully', [
                'task_id' => $this->task->id,
                'ai_data' => $aiData
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process AI task', [
                'task_id' => $this->task->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['ai', 'task:' . $this->task->id];
    }
}
