<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;

    protected TaskRepositoryInterface $repo;
    protected TaskService $taskService;

    public function __construct(TaskRepositoryInterface $repo, TaskService $taskService)
    {
        $this->repo = $repo;
        $this->taskService = $taskService;
    }

    protected function middleware(string $middleware)
    {
        // Middleware applied via routes
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = $request->query();

        if ($user->isAdmin()) {
            $tasks = $this->repo->all($filters);
            $users = User::all();
        } else {
            $filters['user_id'] = $user->id;
            $tasks = $this->repo->all($filters);
            $users = null;
        }

        return view('tasks.index', compact('tasks', 'users'));
    }

    public function create()
    {
        $user = auth()->user();
        $this->authorize('create', Task::class);

        $users = $user->isAdmin() ? User::all() : collect([$user]);

        return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date|after:today',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task = $this->taskService->store($validated);

        return redirect()->route('tasks.show', $task)->with('success', 'Task created successfully with AI analysis.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $user = auth()->user();
        $users = $user->isAdmin() ? User::all() : collect([$task->user]);

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $this->repo->update($task->id, $validated);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->repo->delete($task->id);

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
