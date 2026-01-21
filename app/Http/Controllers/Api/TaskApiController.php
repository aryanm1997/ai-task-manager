<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class TaskApiController
{
    use AuthorizesRequests;

    protected TaskRepositoryInterface $repo;
    protected TaskService $taskService;

    public function __construct(TaskRepositoryInterface $repo, TaskService $taskService)
    {
        $this->repo = $repo;
        $this->taskService = $taskService;
    }

    /**
     * Get all tasks with optional filtering
     * 
     * @return AnonymousResourceCollection|JsonResponse
     */

     public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ]
        ]);
    }
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $filters = $request->query();

            // Non-admins can only see their own tasks
            if (!$user->isAdmin()) {
                $filters['user_id'] = $user->id;
            }

            $tasks = $this->repo->all($filters);

            return TaskResource::collection($tasks);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve tasks',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new task
     * 
     * @param StoreTaskRequest $request
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        try {
            $this->authorize('create', Task::class);

            $validated = $request->validated();
            $task = $this->taskService->store($validated);

            return response()->json(
                new TaskResource($task),
                201
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create task',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific task
     * 
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task)
    {
        try {
            $this->authorize('view', $task);

            return response()->json(
                new TaskResource($task),
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve task',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update task status
     * 
     * @param UpdateTaskStatusRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        try {
            $this->authorize('update', $task);

            $validated = $request->validated();
            $this->repo->update($task->id, $validated);
            
            $task = $this->repo->find($task->id);

            return response()->json(
                new TaskResource($task),
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update task status',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI summary for a task
     * 
     * @param Task $task
     * @return JsonResponse
     */
    public function getAiSummary(Task $task)
    {
        try {
            $this->authorize('view', $task);

            return response()->json([
                'id' => $task->id,
                'title' => $task->title,
                'ai_summary' => $task->ai_summary,
                'ai_priority' => $task->ai_priority?->value,
                'generated_at' => $task->updated_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve AI summary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a task
     * 
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task)
    {
        try {
            $this->authorize('delete', $task);

            $this->repo->delete($task->id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete task',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
