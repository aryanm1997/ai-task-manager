@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Tasks</h1>
        @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                + New Task
            </a>
        @endif
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ $message }}
        </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 bg-gray-100 p-4 rounded">
        <form method="GET" action="{{ route('tasks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Priority</label>
                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">All Priority</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
            @if (auth()->user()->isAdmin() && $users)
                <div>
                    <label class="block text-sm font-bold mb-1">Assigned To</label>
                    <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tasks Table -->
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Assigned To</th>
                    <th class="px-6 py-3 text-left">Priority</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Due Date</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold">{{ $task->title }}</td>
                        <td class="px-6 py-4">{{ $task->user->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded text-black text-sm font-bold
                                {{ $task->priority->value === 'high' ? 'bg-red-500' : ($task->priority->value === 'medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                {{ ucfirst($task->priority->value) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded text-white text-sm font-bold
                                {{ $task->status->value === 'completed' ? 'bg-green-500' : ($task->status->value === 'in_progress' ? 'bg-blue-500' : 'bg-gray-500') }}">
                                {{ str_replace('_', ' ', ucfirst($task->status->value)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $task->due_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('tasks.show', $task) }}" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded text-sm">
                                View
                            </a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <a href="{{ route('tasks.edit', $task) }}" class="bg-yellow-500 hover:bg-yellow-700 text-black py-1 px-3 rounded text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-black py-1 px-3 rounded text-sm">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No tasks found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $tasks->links() }}
    </div>
</div>
@endsection
