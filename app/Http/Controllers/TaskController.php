<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::all();
            return response()->json(['data' => $tasks], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch tasks',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Build validation rules
        $rules = [
            'task_name' => 'required|string|max:255',
            'status' => 'boolean',
        ];
        
        // Only validate description if it's present
        if ($request->has('description')) {
            $rules['description'] = 'string';
        }

        $this->validate($request, $rules);

        try {
            $task = Task::create([
                'task_name' => $request->get('task_name'),
                'description' => $request->get('description'),
                'status' => $request->get('status', false),
            ]);

            return response()->json([
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create task',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $task = Task::find($id);
            
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }

            return response()->json(['data' => $task], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch task'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Build validation rules
        $rules = [
            'task_name' => 'required|string|max:255',
            'status' => 'boolean',
        ];
        
        // Only validate description if it's present
        if ($request->has('description')) {
            $rules['description'] = 'string';
        }

        $this->validate($request, $rules);

        try {
            $task = Task::find($id);
            
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }

            $task->update([
                'task_name' => $request->get('task_name'),
                'description' => $request->get('description'),
                'status' => $request->get('status', false),
            ]);

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update task'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::find($id);
            
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }

            $task->delete();

            return response()->json(['message' => 'Task deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete task'], 500);
        }
    }
}
