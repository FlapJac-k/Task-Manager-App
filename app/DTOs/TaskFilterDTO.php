<?php

namespace App\DTOs;

class TaskFilterDTO
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $due_from = null,
        public readonly ?string $due_to = null,
        public readonly ?bool $with_dependencies = null,
        public readonly ?int $task_id = null,
        public readonly ?int $assigned_to = null,
    ) {}
}
