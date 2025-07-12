<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(private readonly TaskRepositoryInterface $taskRepository) {}

    public function getVisibleTasks(User $user): Collection
    {
        return $user->hasRole('manager')
            ? $this->taskRepository->getAll()
            : $this->taskRepository->getByAssignedUser($user->id);
    }

    public function createTask(array $data): Task
    {
        $dependencies = $data['depends_on'] ?? [];
        unset($data['depends_on']);

        $task = $this->taskRepository->create($data);

        if (!empty($dependencies)) {
            $this->syncDependencies($task, $dependencies);
        }

        return $task;
    }

    public function updateTask(Task $task, array $data, User $user): Task
    {
        $this->validateCompletionRules($task, $data);

        if ($user->hasRole('manager')) {

            $dependencies = $data['depends_on'] ?? null;

            unset($data['depends_on']);

            $this->taskRepository->update($task, $data);

            if ($dependencies !== null) {
                $this->syncDependencies($task, $dependencies);
            }

            return $task;
        }

        if ($user->hasRole('user') && $task->assigned_to === $user->id) {
            if (!isset($data['status'])) {
                throw ValidationException::withMessages([
                    'status' => ['You can only update the task status.']
                ]);
            }

            return $this->taskRepository->update($task, [
                'status' => $data['status'],
            ]);
        }

        throw ValidationException::withMessages([
            'permission' => ['You are not authorized to update this task.']
        ]);
    }

    private function syncDependencies(Task $task, array $dependencies): void
    {
        if (in_array($task->id, $dependencies)) {
            throw ValidationException::withMessages([
                'depends_on' => ['A task cannot depend on itself.']
            ]);
        }

        $task->dependencies()->sync($dependencies);
    }

    private function validateCompletionRules(Task $task, array $data): void
    {
        //TODO:: Use Enum For Task status
        if (
            isset($data['status']) &&
            $data['status'] === 'completed' &&
            $task->dependencies()->where('status', '!=', 'completed')->exists()
        ) {
            throw ValidationException::withMessages([
                'status' => ['Cannot complete task until all dependencies are completed.']
            ]);
        }
    }
}
