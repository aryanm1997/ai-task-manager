<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Return true if any authenticated user can create a task
        return auth()->check();  
        // Or implement more logic:
        // return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date|after:today',
            'assigned_to' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Custom messages for validation errors (optional).
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'title.max' => 'Task title must not exceed 255 characters.',
            'description.required' => 'Task description is required',
            'description.max' => 'Task description must not exceed 5000 characters.',
            'priority.required' => 'Please select a priority',
            'priority.in' => 'Priority must be low, medium, or high',
            'due_date.required' => 'Due date is required',
            'due_date.date' => 'Due date must be a valid date',
            'due_date.after' => 'Due date must be in the future',
            'assigned_to.required' => 'User assignment is required',
            'assigned_to.exists' => 'The selected user does not exist.',
        ];
    }

    /**
     * Modify the data before validation (optional).
     */
    protected function prepareForValidation()
    {
        // Example: convert due_date to proper format
        if ($this->due_date) {
            $this->merge([
                'due_date' => date('Y-m-d', strtotime($this->due_date)),
            ]);
        }
    }
}
