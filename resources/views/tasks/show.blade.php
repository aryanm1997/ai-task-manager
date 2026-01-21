@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold">{{ $task->title }}</h1>
                <p class="text-gray-600 mt-2">Assigned to: <strong>{{ $task->user->name }}</strong></p>
            </div>
            @if(Auth::user()->isAdmin())
                <div class="flex gap-2">
                    <a href="{{ route('tasks.edit', $task) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $message }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded shadow p-4">
                <p class="text-gray-600 text-sm">Priority</p>
                <p class="text-lg font-bold">
                    <span class="px-3 py-1 rounded text-black text-sm font-bold
                        {{ $task->priority->value === 'high' ? 'bg-red-500' : ($task->priority->value === 'medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                        {{ ucfirst($task->priority->value) }}
                    </span>
                </p>
            </div>
            <div class="bg-white rounded shadow p-4">
                <p class="text-gray-600 text-sm">Status</p>
                <p class="text-lg font-bold">
                    <span class="px-3 py-1 rounded text-white text-sm font-bold
                        {{ $task->status->value === 'completed' ? 'bg-green-500' : ($task->status->value === 'in_progress' ? 'bg-blue-500' : 'bg-gray-500') }}">
                        {{ str_replace('_', ' ', ucfirst($task->status->value)) }}
                    </span>
                </p>
            </div>
            <div class="bg-white rounded shadow p-4">
                <p class="text-gray-600 text-sm">Due Date</p>
                <p class="text-lg font-bold">{{ $task->due_date->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="bg-white rounded shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Description</h2>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $task->description }}</p>
        </div>

        @if($task->ai_summary)
            <div class="bg-blue-50 rounded shadow p-6 mb-6 border-l-4 border-blue-500">
                <h2 class="text-xl font-bold mb-2 flex items-center">
                    <span class="text-2xl mr-2">🤖</span> AI Analysis
                </h2>
                <div class="mb-4">
                    <p class="text-gray-600 text-sm mb-1">AI Generated Summary</p>
                    <p class="text-gray-800">{{ $task->ai_summary }}</p>
                </div>
                @if($task->ai_priority)
                    <div>
                        <p class="text-gray-600 text-sm mb-1">AI Suggested Priority</p>
                        <p>
                            <span class="px-3 py-1 rounded text-white text-sm font-bold
                                {{ $task->ai_priority->value === 'high' ? 'bg-red-500' : ($task->ai_priority->value === 'medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                {{ ucfirst($task->ai_priority->value) }}
                            </span>
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <div class="flex gap-4">
            @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Task
                </a>
            @endcan
            <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Tasks
            </a>
        </div>
    </div>
</div>
@endsection
