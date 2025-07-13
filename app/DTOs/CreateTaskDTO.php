<?php

namespace App\DTOs;

use App\Enums\TaskStatusEnum;

class CreateTaskDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly ?int $assigned_to = null,
        public readonly ?string $due_date = null,
        public readonly string $status = TaskStatusEnum::Pending->value,
        public readonly array $depends_on = []
    ) {}
}
