<?php

namespace App\Http\Requests;

use App\DTOs\UpdateTaskDTO;
use App\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
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
        $user = $this->user();

        if ($user->hasRole('manager')) {
            return [
                'title' => 'sometimes|required|string|max:50',
                'description' => 'sometimes|nullable|string',
                'status' => ['sometimes', 'required', new Enum(TaskStatusEnum::class)],
                'assigned_to' => 'sometimes|exists:users,id',
                'due_date' => 'sometimes|nullable|date',
                'depends_on' => 'nullable|array',
                'depends_on.*' => 'exists:tasks,id|distinct',
            ];
        }

        return [
            'status' => ['required', new Enum(TaskStatusEnum::class)],
        ];
    }

    public function toDTO(): UpdateTaskDTO
    {
        // undefined so i can filter only the passed values later
        return new UpdateTaskDTO(
            title: $this->has('title') ? $this->input('title') : UpdateTaskDTO::UNDEFINED,
            description: $this->has('description') ? $this->input('description') : UpdateTaskDTO::UNDEFINED,
            assigned_to: $this->has('assigned_to') ? $this->input('assigned_to') : UpdateTaskDTO::UNDEFINED,
            due_date: $this->has('due_date') ? $this->input('due_date') : UpdateTaskDTO::UNDEFINED,
            status: $this->has('status') ? TaskStatusEnum::tryFrom($this->input('status')) : UpdateTaskDTO::UNDEFINED,
            depends_on: $this->has('depends_on') ? $this->input('depends_on') : UpdateTaskDTO::UNDEFINED,
        );
    }
}
