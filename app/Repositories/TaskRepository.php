<?php

namespace App\Repositories;

use App\DTOs\TaskFilterDTO;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
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

    public function filter(TaskFilterDTO $dto): Collection
    {
        $query = Task::query();

        $query
            ->when($dto->task_id, fn ($q) => $q->where('id', $dto->task_id))
            ->when($dto->status, fn ($q) => $q->where('status', $dto->status))
            ->when($dto->due_from, fn ($q) => $q->whereDate('due_date', '>=', $dto->due_from))
            ->when($dto->due_to, fn ($q) => $q->whereDate('due_date', '<=', $dto->due_to))
            ->when($dto->assigned_to, fn ($q) => $q->where('assigned_to', $dto->assigned_to));

        if ($dto->with_dependencies) {
            $query->with('dependencies');
        }

        return $query->get();
    }
}
