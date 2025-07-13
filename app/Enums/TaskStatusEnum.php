<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case Pending = 'pending';
    case InProgress = 'inprogress';
    case Completed = 'completed';
    case Canceled = 'canceled';
}
