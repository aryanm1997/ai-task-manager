<?php

namespace Tests\Feature;

use App\Jobs\ProcessAITask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessAITaskTest extends TestCase
{
    /**
     * Test that creating a task dispatches a ProcessAITask job.
     */
    public function test_creating_task_dispatches_ai_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 'medium',
        ]);

        Queue::assertPushed(ProcessAITask::class);
    }

    /**
     * Test that ProcessAITask job processes correctly.
     */
    public function test_process_ai_task_updates_task(): void
    {
        $task = Task::factory()->create([
            'ai_summary' => null,
            'ai_priority' => null,
        ]);

        $job = new ProcessAITask($task);
        $job->handle(
            app('App\Services\AIService'),
            app('App\Repositories\Contracts\TaskRepositoryInterface')
        );

        $task->refresh();

        $this->assertNotNull($task->ai_summary);
        $this->assertNotNull($task->ai_priority);
    }

    /**
     * Test that ProcessAITask job includes proper tags.
     */
    public function test_process_ai_task_has_tags(): void
    {
        $task = Task::factory()->create();
        $job = new ProcessAITask($task);

        $tags = $job->tags();

        $this->assertContains('ai', $tags);
        $this->assertContains('task:' . $task->id, $tags);
    }
}
