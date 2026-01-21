<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                @if(Auth::user()->isAdmin())
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Admin Dashboard - Full Task Management</p>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Your Assigned Tasks</p>
                @endif
            </div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + New Task
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-2">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        @if(Auth::user()->isAdmin())
                            <strong>Admin Access</strong> - You have full access to manage all tasks and users.
                        @else
                            You have {{ Auth::user()->tasks()->count() }} task(s) assigned to you.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Total Tasks</h4>
                        <p class="text-3xl font-bold mt-2">{{ $statistics['total'] }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Pending</h4>
                        <p class="text-3xl font-bold mt-2 text-yellow-500">{{ $statistics['pending'] }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="text-gray-600 dark:text-gray-400 text-sm font-semibold">In Progress</h4>
                        <p class="text-3xl font-bold mt-2 text-blue-500">{{ $statistics['inProgress'] }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Completed</h4>
                        <p class="text-3xl font-bold mt-2 text-green-500">{{ $statistics['completed'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Completion Rate Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h4 class="text-lg font-semibold mb-4">Task Completion Rate</h4>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-green-500 h-4 rounded-full transition-all duration-500" 
                                     style="width: {{ $statistics['completionRate'] }}%"></div>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-green-500 whitespace-nowrap">{{ $statistics['completionRate'] }}%</div>
                    </div>
                </div>
            </div>
            

            @if(Auth::user()->isAdmin())
                <!-- ADMIN DASHBOARD -->
                
                <!-- All Tasks Management -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">All Tasks</h3>
                            <a href="{{ route('tasks.index') }}" class="text-blue-500 hover:text-blue-700 text-sm font-semibold">View All Tasks →</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="border-b bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="text-left py-3 px-4">Title</th>
                                        <th class="text-left py-3 px-4">Assigned To</th>
                                        <th class="text-left py-3 px-4">Priority</th>
                                        <th class="text-left py-3 px-4">Status</th>
                                        <th class="text-left py-3 px-4">Due Date</th>
                                        <th class="text-left py-3 px-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTasks as $task)
                                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="py-3 px-4 font-semibold">{{ $task->title }}</td>
                                            <td class="py-3 px-4">{{ $task->user->name }}</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded text-xs font-bold text-white
                                                    {{ $task->priority->value === 'high' ? 'bg-red-500' : ($task->priority->value === 'medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                                    {{ ucfirst($task->priority->value) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded text-xs font-bold text-white
                                                    {{ $task->status->value === 'completed' ? 'bg-green-500' : ($task->status->value === 'in_progress' ? 'bg-blue-500' : 'bg-gray-500') }}">
                                                    {{ str_replace('_', ' ', ucfirst($task->status->value)) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">{{ $task->due_date->format('M d, Y') }}</td>
                                            <td class="py-3 px-4">
                                                <div class="flex gap-2">
                                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-500 hover:text-blue-700 text-xs font-semibold">View</a>
                                                    <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-500 hover:text-yellow-700 text-xs font-semibold">Edit</a>
                                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-4 px-4 text-center text-gray-500">No tasks yet. Create one to get started!</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @else
                <!-- USER DASHBOARD (READ-ONLY) -->

                <!-- Your Tasks (Read-Only List) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Your Assigned Tasks</h3>
                            <a href="{{ route('tasks.index') }}" class="text-blue-500 hover:text-blue-700 text-sm font-semibold">View All →</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="border-b bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="text-left py-3 px-4">Title</th>
                                        <th class="text-left py-3 px-4">Priority</th>
                                        <th class="text-left py-3 px-4">Status</th>
                                        <th class="text-left py-3 px-4">Due Date</th>
                                        <th class="text-left py-3 px-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTasks as $task)
                                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="py-3 px-4 font-semibold">{{ $task->title }}</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded text-xs font-bold text-black
                                                    {{ $task->priority->value === 'high' ? 'bg-red-500' : ($task->priority->value === 'medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                                    {{ ucfirst($task->priority->value) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded text-xs font-bold text-white
                                                    {{ $task->status->value === 'completed' ? 'bg-green-500' : ($task->status->value === 'in_progress' ? 'bg-blue-500' : 'bg-gray-500') }}">
                                                    {{ str_replace('_', ' ', ucfirst($task->status->value)) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">{{ $task->due_date->format('M d, Y') }}</td>
                                            <td class="py-3 px-4">
                                                <a href="{{ route('tasks.show', $task) }}" class="text-blue-500 hover:text-blue-700 text-xs font-semibold">View Details</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 px-4 text-center text-gray-500">No tasks assigned yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>

    <script>
        // Chart.js configuration
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: {
                        color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                        font: {
                            size: 12,
                            weight: '200'
                        }
                    }
                }
            }
        };

        // Status Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const statusData = {!! $statusChart !!};
            new Chart(statusCtx, {
                type: 'doughnut',
                data: statusData,
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#ddd',
                            borderWidth: 1
                        }
                    }
                }
            });
        }

        // Priority Chart
        const priorityCtx = document.getElementById('priorityChart');
        if (priorityCtx) {
            const priorityData = {!! $priorityChart !!};
            new Chart(priorityCtx, {
                type: 'doughnut',
                data: priorityData,
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#ddd',
                            borderWidth: 1
                        }
                    }
                }
            });
        }

        // Completion Chart
        const completionCtx = document.getElementById('completionChart');
        if (completionCtx) {
            const completionData = {!! $completionChart !!};
            new Chart(completionCtx, {
                type: 'line',
                data: completionData,
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                            },
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(200, 200, 200, 0.2)'
                            }
                        },
                        x: {
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                            },
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(200, 200, 200, 0.2)'
                            }
                        }
                    }
                }
            });
        }

        // Users Chart (Admin only)
        const usersCtx = document.getElementById('usersChart');
        if (usersCtx) {
            const usersData = {!! $usersChart ?? 'null' !!};
            if (usersData) {
                new Chart(usersCtx, {
                    type: 'bar',
                    data: usersData,
                    options: {
                        ...chartOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(200, 200, 200, 0.2)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(200, 200, 200, 0.2)'
                                }
                            }
                        }
                    }
                });
            }
        }
    </script>
</x-app-layout>
