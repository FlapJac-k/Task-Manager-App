<?php

namespace App\Services;

use App\DTOs\CreateTaskDTO;
use App\DTOs\TaskFilterDTO;
use App\DTOs\UpdateTaskDTO;
use App\Enums\TaskStatusEnum;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(private readonly TaskRepositoryInterface $taskRepository) {}

    public function getFilteredTasks(TaskFilterDTO $dto): Collection
    {
        return $this->taskRepository->filter($dto);
    }

    public function createTask(CreateTaskDTO $dto): Task
    {
        $task = $this->taskRepository->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'assigned_to' => $dto->assigned_to,
            'due_date' => $dto->due_date,
            'status' => $dto->status,
        ]);

        if (! empty($dto->depends_on)) {
            $this->syncDependencies($task, $dto->depends_on);
        }

        return $task;
    }

    public function updateTask(Task $task, UpdateTaskDTO $dto, User $user): Task
    {
        $this->validateCompletionRules($task, $dto);

        if ($user->hasRole('manager')) {

            $updateData = [
                'title' => $dto->title,
                'description' => $dto->description,
                'assigned_to' => $dto->assigned_to,
                'due_date' => $dto->due_date,
                'status' => $dto->status instanceof TaskStatusEnum ? $dto->status->value : UpdateTaskDTO::UNDEFINED,
            ];

            $updateData = array_filter($updateData, fn ($v) => $v !== null && $v !== UpdateTaskDTO::UNDEFINED);

            $this->taskRepository->update($task, $updateData);

            if ($dto->depends_on != UpdateTaskDTO::UNDEFINED) {
                $this->syncDependencies($task, $dto->depends_on);
            }

            return $task;
        }

        if ($user->hasRole('user') && $task->assigned_to === $user->id) {
            if (! $dto->status instanceof TaskStatusEnum) {
                throw ValidationException::withMessages([
                    'status' => ['You can only update the task status.'],
                ]);
            }

            return $this->taskRepository->update($task, [
                'status' => $dto->status->value,
            ]);
        }

        throw ValidationException::withMessages([
            'permission' => ['You are not authorized to update this task.'],
        ]);
    }

    public function deleteTask(Task $task): void
    {

        if ($task->dependents()->exists()) {
            throw ValidationException::withMessages([
                'task' => ['Cannot delete task because other tasks depend on it.'],
            ]);
        }

        $this->taskRepository->delete($task);
    }

    private function syncDependencies(Task $task, array $dependencies): void
    {
        if (in_array($task->id, $dependencies)) {
            throw ValidationException::withMessages([
                'depends_on' => ['A task cannot depend on itself.'],
            ]);
        }

        $task->dependencies()->sync($dependencies);
    }

    private function validateCompletionRules(Task $task, UpdateTaskDTO $dto): void
    {
        if (
            $dto->status === TaskStatusEnum::Completed &&
            $task->dependencies()->where('status', '!=', TaskStatusEnum::Completed->value)->exists()
        ) {
            throw ValidationException::withMessages([
                'status' => ['Cannot complete task until all dependencies are completed.'],
            ]);
        }
    }
}
