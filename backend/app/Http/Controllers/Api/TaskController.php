<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    use ApiResponse;

    public function __construct(protected TaskRepositoryInterface $taskRepository) {}
    /**
 
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $this->taskRepository->getForUserPaginated($request->user(), 10);
        return $this->successResponse(TaskResource::collection($tasks), 'Task retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskRepository->createForUser($request->user(), $request->validated());
        return $this->successResponse(new TaskResource($task), 'Task created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);

        // if ($task) {
        //     return $this->errorResponse('Task not found', 404);
        // }

        // if ($task->user_id !== $request->user()->id) {
        //     return $this->errorResponse('Unauthorized', 403);
        // }

        return $this->successResponse(new TaskResource($task), 'Task retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
