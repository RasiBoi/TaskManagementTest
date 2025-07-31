<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    /**
     * Auto-categorize a task based on its name and description
     * (Imported from TaskCategorizerController for integration)
     */
    private function autoCategorizeTask($task)
    {
        // Extract task name and description, convert to lowercase for matching
        $taskName = strtolower(isset($task['task_name']) ? $task['task_name'] : '');
        $description = strtolower(isset($task['description']) ? $task['description'] : '');
        
        // Combine both fields for comprehensive keyword matching
        $combinedText = $taskName . ' ' . $description;
        
        // Define keyword categories with comprehensive keyword lists
        $categories = [
            'Work' => [
                'project', 'meeting', 'presentation', 'report', 'deadline', 'client', 'proposal',
                'budget', 'review', 'analysis', 'development', 'design', 'implementation',
                'testing', 'deployment', 'documentation', 'conference', 'interview', 'email',
                'call', 'planning', 'strategy', 'marketing', 'sales', 'finance', 'accounting',
                'api', 'backend', 'frontend', 'database', 'server', 'code', 'programming',
                'laravel', 'php', 'javascript', 'html', 'css', 'sql', 'mysql', 'office'
            ],
            'Personal' => [
                'family', 'friend', 'birthday', 'vacation', 'travel', 'home', 'house', 'garden',
                'cleaning', 'cooking', 'shopping', 'grocery', 'doctor', 'dentist', 'appointment',
                'health', 'exercise', 'gym', 'workout', 'hobby', 'movie', 'music', 'game',
                'sports', 'personal', 'bills', 'rent', 'car', 'pet'
            ],
            'Learning' => [
                'learn', 'study', 'course', 'tutorial', 'training', 'education', 'class',
                'lesson', 'workshop', 'certification', 'exam', 'book', 'reading', 'research',
                'skill', 'practice', 'homework', 'university', 'college', 'language'
            ]
        ];
        
        // Score each category based on keyword matches
        $categoryScores = [];
        
        foreach ($categories as $category => $keywords) {
            $score = 0;
            
            foreach ($keywords as $keyword) {
                if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $combinedText)) {
                    $score++;
                    if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $taskName)) {
                        $score += 0.5;
                    }
                }
            }
            
            $categoryScores[$category] = $score;
        }
        
        // Find the category with the highest score
        $bestCategory = 'Other';
        $maxScore = 0;
        
        foreach ($categoryScores as $category => $score) {
            if ($score > $maxScore) {
                $maxScore = $score;
                $bestCategory = $category;
            }
        }
        
        return $maxScore > 0 ? $bestCategory : 'Other';
    }
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
            // Auto-categorize the task
            $taskData = [
                'task_name' => $request->get('task_name'),
                'description' => $request->get('description', '')
            ];
            $category = $this->autoCategorizeTask($taskData);
            
            $task = Task::create([
                'task_name' => $request->get('task_name'),
                'description' => $request->get('description'),
                'status' => $request->get('status', false),
                'category' => $category,
            ]);

            return response()->json([
                'message' => 'Task created successfully',
                'data' => $task,
                'auto_categorized_as' => $category
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

            // Auto-categorize the updated task
            $taskData = [
                'task_name' => $request->get('task_name'),
                'description' => $request->get('description', '')
            ];
            $category = $this->autoCategorizeTask($taskData);

            $task->update([
                'task_name' => $request->get('task_name'),
                'description' => $request->get('description'),
                'status' => $request->get('status', false),
                'category' => $category,
            ]);

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => $task,
                'auto_categorized_as' => $category
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
