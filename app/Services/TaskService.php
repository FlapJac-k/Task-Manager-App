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
        if ($user->hasRole('manager')) {
            return $this->taskRepository->getAll();
        }

        return $this->taskRepository->getByAssignedUser($user->id);
    }

    public function updateTask(Task $task, array $data, User $user): Task
    {
        //TODO:: Use Enum For Task status
        if ($task->dependencies()->where('status', '!=', 'completed')->exists()) {
            throw ValidationException::withMessages([
                'status' => ['Cannot complete task until all dependencies are completed.'],
            ]);
        }

        if ($user->hasRole('manager')) {
            return $this->taskRepository->update($task, $data);
        }

        if ($user->hasRole('user') && $task->assigned_to === $user->id) {
            return $this->taskRepository->update($task, [
                'status' => $data['status']
            ]);
        }

        throw ValidationException::withMessages([
            'permission' => ['You are not authorized to update this task.']
        ]);
    }
}
