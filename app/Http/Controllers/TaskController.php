<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(GetTaskRequest $request)
    {

        $dto = $request->toDTO();

        $user = $request->user();

        $tasks = $this->taskService->getFilteredTasks($dto, $user);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $dto = $request->toDTO();

        $task = $this->taskService->createTask($dto);

        return (new TaskResource($task))->response()->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $dto = $request->toDTO();

        $user = $request->user();

        $updatedTask = $this->taskService->updateTask($task, $dto, $user);

        return new TaskResource($updatedTask);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->taskService->deleteTask($task);

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
