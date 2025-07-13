<?php

namespace App\Http\Requests;

use App\DTOs\TaskFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class GetTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:pending,inprogress,completed,canceled',
            'due_from' => 'nullable|date',
            'due_to' => 'nullable|date|after_or_equal:due_from',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'task_id' => 'nullable|integer|exists:tasks,id',
            'with_dependencies' => 'sometimes|boolean',
        ];
    }

    public function toDTO(): TaskFilterDTO
    {
        $user = $this->user();

        return new TaskFilterDTO(
            status: $this->input('status'),
            due_from: $this->input('due_from'),
            due_to: $this->input('due_to'),
            assigned_to: $user->hasRole('manager') ? $this->input('assigned_to') : $user->id,
            task_id: $this->input('task_id'),
            with_dependencies: filter_var($this->input('with_dependencies', false), FILTER_VALIDATE_BOOLEAN)
        );
    }
}
