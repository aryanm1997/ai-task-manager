<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'ai_summary',
        'ai_priority',
        'completed_at',
    ];

    protected $casts = [
        'priority' => TaskPriority::class,
        'status' => TaskStatus::class,
        'ai_priority' => TaskPriority::class,
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function ($task) {
            // Set completed_at when status is changed to completed
            if ($task->isDirty('status') && $task->status->value === 'completed' && !$task->completed_at) {
                $task->completed_at = Carbon::now();
            }

            // Clear completed_at if status is changed away from completed
            if ($task->isDirty('status') && $task->status->value !== 'completed' && $task->completed_at) {
                $task->completed_at = null;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeFilter($query, array $filters = [])
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('assigned_to', $filters['user_id']);
        }

        return $query;
    }
}
