<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAll(): Collection
    {
        return Task::all();
    }

    public function getByAssignedUser(int $userId): Collection
    {
        return Task::where('assigned_to', $userId)->get();
    }

    public function findWithDependencies(int $taskId): Task
    {
        return Task::with('dependencies')->findOrFail($taskId);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
