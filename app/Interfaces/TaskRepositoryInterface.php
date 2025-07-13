<?php

namespace App\Interfaces;

use App\DTOs\TaskFilterDTO;
use App\Models\Task;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function filter(TaskFilterDTO $dto): Collection;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): void;
}
