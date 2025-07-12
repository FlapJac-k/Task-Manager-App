<?php

namespace App\Interfaces;

use App\Models\Task;
use Illuminate\Support\Collection;


interface TaskRepositoryInterface
{

    public function getAll(): Collection;

    public function getByAssignedUser(int $userId): Collection;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;
}
