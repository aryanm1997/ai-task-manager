<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get task status distribution data
     */
    public function getTaskStatusChart(User $user = null): array
    {
        $query = Task::query();

        if ($user && !$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        $pending = $query->clone()->where('status', 'pending')->count();
        $inProgress = $query->clone()->where('status', 'in_progress')->count();
        $completed = $query->clone()->where('status', 'completed')->count();

        return [
            'labels' => ['Pending', 'In Progress', 'Completed'],
            'datasets' => [
                [
                    'label' => 'Tasks by Status',
                    'data' => [$pending, $inProgress, $completed],
                    'backgroundColor' => [
                        'rgba(234, 179, 8, 0.8)',    // yellow
                        'rgba(59, 130, 246, 0.8)',   // blue
                        'rgba(34, 197, 94, 0.8)',    // green
                    ],
                    'borderColor' => [
                        'rgb(234, 179, 8)',
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                    ],
                    'borderWidth' => 2,
                ]
            ],
        ];
    }

    /**
     * Get task priority distribution
     */
    public function getTaskPriorityChart(User $user = null): array
    {
        $query = Task::query();

        if ($user && !$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        $high = $query->clone()->where('priority', 'high')->count();
        $medium = $query->clone()->where('priority', 'medium')->count();
        $low = $query->clone()->where('priority', 'low')->count();

        return [
            'labels' => ['High', 'Medium', 'Low'],
            'datasets' => [
                [
                    'label' => 'Tasks by Priority',
                    'data' => [$high, $medium, $low],
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',     // red
                        'rgba(234, 179, 8, 0.8)',     // yellow
                        'rgba(34, 197, 94, 0.8)',     // green
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(234, 179, 8)',
                        'rgb(34, 197, 94)',
                    ],
                    'borderWidth' => 2,
                ]
            ],
        ];
    }

    /**
     * Get monthly task completion data
     */
    public function getMonthlyCompletionChart(User $user = null, int $months = 12): array
    {
        $query = Task::query();

        if ($user && !$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        $data = [];
        $labels = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->clone()->startOfMonth();
            $endOfMonth = $date->clone()->endOfMonth();

            $completed = $query->clone()
                ->where('status', 'completed')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                ->count();

            $data[] = $completed;
            $labels[] = $date->format('M Y');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Completed Tasks',
                    'data' => $data,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                    'pointHoverRadius' => 7,
                ]
            ],
        ];
    }

    /**
     * Get tasks by user (admin only)
     */
    public function getTasksByUserChart(): array
    {
        $users = User::withCount(['tasks' => function ($query) {
            $query->where('status', 'completed');
        }])
            ->withCount('tasks')
            ->orderByDesc('tasks_count')
            ->limit(10)
            ->get();

        $labels = $users->pluck('name')->toArray();
        $completed = $users->pluck('tasks_count')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Completed Tasks',
                    'data' => $completed,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ]
            ],
        ];
    }

    /**
     * Get overall task statistics
     */
    public function getTaskStatistics(User $user = null): array
    {
        $query = Task::query();

        if ($user && !$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        $total = $query->clone()->count();
        $completed = $query->clone()->where('status', 'completed')->count();
        $pending = $query->clone()->where('status', 'pending')->count();
        $inProgress = $query->clone()->where('status', 'in_progress')->count();

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'inProgress' => $inProgress,
            'completionRate' => $completionRate,
        ];
    }
}
