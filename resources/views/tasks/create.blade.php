@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Create New Task</h1>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tasks.store') }}" method="POST" class="bg-white rounded shadow p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="title">Title</label>
                <input class="w-full px-3 py-2 border border-gray-300 rounded @error('title') border-red-500 @enderror" 
                       type="text" name="title" id="title" value="{{ old('title') }}" required>
                @error('title')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="description">Description</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded @error('description') border-red-500 @enderror" 
                          name="description" id="description" rows="5" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold mb-2" for="priority">Priority</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded @error('priority') border-red-500 @enderror" 
                            name="priority" id="priority" required>
                        <option value="">Select Priority</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2" for="due_date">Due Date</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded @error('due_date') border-red-500 @enderror" 
                           type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required>
                    @error('due_date')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2" for="assigned_to">Assign To</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded @error('assigned_to') border-red-500 @enderror" 
                        name="assigned_to" id="assigned_to" required>
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                    Create Task
                </button>
                <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
