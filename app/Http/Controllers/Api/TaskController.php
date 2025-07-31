<?php

// app/Http/Controllers/Api/TaskController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Get all tasks.
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks, 200);
    }

    /**
     * Create a new task.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'task_name' => 'required|string|max:255', // task_name is required [cite: 29]
            'description' => 'nullable|string',
        ]);

        // If validation fails, return error response [cite: 30]
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create and save the task
        $task = Task::create($request->all());

        // Return the new task with a 201 Created status code [cite: 22]
        return response()->json($task, 201);
    }

    /**
     * Update an existing task's status.
     */
    public function update(Request $request, $id)
    {
        // Find the task by ID
        $task = Task::find($id);

        // If task not found, return 404
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Validate the status field
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update task status
        $task->status = $request->status;
        $task->save();

        // Return the updated task
        return response()->json($task, 200);
    }

    /**
     * Delete a task.
     */
    public function destroy($id)
    {
        // Find and delete the task
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        
        $task->delete();

        // Return a success message with a 200 OK status
        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
