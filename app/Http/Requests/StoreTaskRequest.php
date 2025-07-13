<?php

namespace App\Http\Requests;

use App\DTOs\CreateTaskDTO;
use App\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => [new Enum(TaskStatusEnum::class)],
            'depends_on' => 'nullable|array',
            'depends_on.*' => 'exists:tasks,id|distinct',
        ];
    }

    public function toDTO(): CreateTaskDTO
    {

        return new CreateTaskDTO(
            title: $this->input('title'),
            description: $this->input('description'),
            assigned_to: $this->input('assigned_to'),
            due_date: $this->input('due_date'),
            status: $this->input('status', 'pending'),
            depends_on: $this->input('depends_on', [])
        );
    }
}
