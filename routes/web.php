<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $dashboardService = app('App\Services\DashboardService');
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        $recentTasks = Task::latest()->take(5)->get();
    } else {
        $recentTasks = $user->tasks()->latest()->take(5)->get();
    }

    // Get chart data
    $statusChart = json_encode($dashboardService->getTaskStatusChart($user->isAdmin() ? null : $user));
    $priorityChart = json_encode($dashboardService->getTaskPriorityChart($user->isAdmin() ? null : $user));
    $completionChart = json_encode($dashboardService->getMonthlyCompletionChart($user->isAdmin() ? null : $user, 12));
    $statistics = $dashboardService->getTaskStatistics($user->isAdmin() ? null : $user);
    $usersChart = $user->isAdmin() ? json_encode($dashboardService->getTasksByUserChart()) : null;

    return view('dashboard', compact('recentTasks', 'statusChart', 'priorityChart', 'completionChart', 'statistics', 'usersChart'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Task routes
    Route::resource('tasks', TaskController::class);
});

require __DIR__.'/auth.php';
