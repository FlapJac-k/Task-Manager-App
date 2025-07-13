<?php

namespace App\DTOs;

use App\Enums\TaskStatusEnum;

class UpdateTaskDTO
{
    public const UNDEFINED = '__undefined__';

    public function __construct(
        public readonly mixed $title = self::UNDEFINED,
        public readonly mixed $description = self::UNDEFINED,
        public readonly mixed $assigned_to = self::UNDEFINED,
        public readonly mixed $due_date = self::UNDEFINED,
        public readonly TaskStatusEnum|string $status = self::UNDEFINED,
        public readonly mixed $depends_on = self::UNDEFINED,
    ) {}
}
