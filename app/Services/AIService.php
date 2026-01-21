<?php

namespace App\Services;

use App\Models\Task;

class AIService
{
    /**
     * Generate AI-based summary and priority for a task
     */
    public function generateSummary(Task $task): array
    {
        // Later you can replace this logic with OpenAI / Gemini / any AI API
        return [
            'ai_summary'  => 'AI generated summary for ' . $task->title,
            'ai_priority' => 'high',
        ];
    }
}
