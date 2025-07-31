<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAICategorizationService;

class TaskCategorizerController extends Controller
{
    /**
     * Automatically categorize a task using OpenAI or fallback to keywords
     *
     * @param array $task
     * @return string
     */
    public function autoCategorizeTask($task)
    {
        $apiKey = env('OPENAI_API_KEY');
        
        if ($this->isValidOpenAIKey($apiKey)) {
            // Use OpenAI for categorization
            try {
                $openAIService = new OpenAICategorizationService($apiKey);
                return $openAIService->categorizeTask($task);
            } catch (Exception $e) {
                // Fall back to keyword-based categorization if OpenAI fails
                return $this->fallbackCategorization($task);
            }
        } else {
            // Use fallback categorization if no API key
            return $this->fallbackCategorization($task);
        }
    }

    /**
     * Check if OpenAI API key is valid
     */
    private function isValidOpenAIKey($key)
    {
        return !empty($key) && 
               $key !== 'your_openai_api_key_here' && 
               (strpos($key, 'sk-') === 0 || strpos($key, 'skproj') === 0);
    }

    /**
     * Fallback categorization using keywords
     */
    private function fallbackCategorization($task)
    {
        // Extract task name and description, convert to lowercase for matching
        $taskName = strtolower(isset($task['task_name']) ? $task['task_name'] : '');
        $description = strtolower(isset($task['description']) ? $task['description'] : '');
        
        // Combine both fields for comprehensive keyword matching
        $combinedText = $taskName . ' ' . $description;
        
        // Define keyword categories with comprehensive keyword lists
        $categories = [
            'Work' => [
                // Professional/Business keywords
                'project', 'meeting', 'presentation', 'report', 'deadline', 'client', 'proposal',
                'budget', 'review', 'analysis', 'development', 'design', 'implementation',
                'testing', 'deployment', 'documentation', 'conference', 'interview', 'email',
                'call', 'planning', 'strategy', 'marketing', 'sales', 'finance', 'accounting',
                'hr', 'management', 'leadership', 'team', 'collaboration', 'workflow',
                'api', 'backend', 'frontend', 'database', 'server', 'code', 'programming',
                'software', 'application', 'website', 'system', 'framework', 'library',
                'laravel', 'php', 'javascript', 'html', 'css', 'sql', 'mysql', 'postgresql',
                'office', 'excel', 'powerpoint', 'word', 'spreadsheet', 'invoice', 'contract'
            ],
            
            'Personal' => [
                // Personal life keywords
                'family', 'friend', 'birthday', 'anniversary', 'vacation', 'travel', 'trip',
                'home', 'house', 'garden', 'cleaning', 'cooking', 'shopping', 'grocery',
                'doctor', 'dentist', 'appointment', 'health', 'exercise', 'gym', 'workout',
                'hobby', 'entertainment', 'movie', 'music', 'game', 'sports', 'football',
                'basketball', 'tennis', 'running', 'walking', 'hiking', 'swimming',
                'personal', 'self', 'goals', 'resolution', 'habit', 'routine', 'lifestyle',
                'finance personal', 'bank', 'bills', 'rent', 'mortgage', 'insurance',
                'car', 'vehicle', 'maintenance', 'repair', 'pet', 'dog', 'cat'
            ],
            
            'Learning' => [
                // Educational/Learning keywords
                'learn', 'study', 'course', 'tutorial', 'training', 'education', 'class',
                'lesson', 'workshop', 'seminar', 'certification', 'exam', 'test', 'quiz',
                'book', 'reading', 'research', 'article', 'blog', 'documentation',
                'skill', 'knowledge', 'practice', 'exercise', 'homework', 'assignment',
                'degree', 'diploma', 'certificate', 'academy', 'university', 'college',
                'school', 'instructor', 'teacher', 'professor', 'mentor', 'coaching',
                'technology', 'programming language', 'framework', 'tool', 'software',
                'online course', 'mooc', 'udemy', 'coursera', 'youtube', 'video',
                'language', 'english', 'spanish', 'french', 'german', 'chinese'
            ]
        ];
        
        // Score each category based on keyword matches
        $categoryScores = [];
        
        foreach ($categories as $category => $keywords) {
            $score = 0;
            
            foreach ($keywords as $keyword) {
                // Check for exact word matches (not just substring)
                if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $combinedText)) {
                    $score++;
                    
                    // Give extra weight if keyword appears in task name
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
        
        // If no keywords matched, return 'Other'
        return $maxScore > 0 ? $bestCategory : 'Other';
    }
    
    /**
     * API endpoint to categorize a task
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categorizeTask(Request $request)
    {
        // Build validation rules compatible with Laravel 5.2
        $rules = [
            'task_name' => 'required|string|max:255',
        ];
        
        // Only validate description if it's present
        if ($request->has('description')) {
            $rules['description'] = 'string';
        }
        
        $this->validate($request, $rules);
        
        $task = [
            'task_name' => $request->get('task_name'),
            'description' => $request->get('description', '')
        ];
        
        $category = $this->autoCategorizeTask($task);
        
        return response()->json([
            'task' => $task,
            'category' => $category
        ]);
    }
    
    /**
     * Test multiple tasks for demonstration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testCategorization()
    {
        $testTasks = [
            [
                'task_name' => 'Finish Laravel project',
                'description' => 'Complete API endpoints and authentication'
            ],
            [
                'task_name' => 'Buy groceries',
                'description' => 'Get milk, bread, and vegetables for dinner'
            ],
            [
                'task_name' => 'Complete online course',
                'description' => 'Finish the JavaScript tutorial on Udemy'
            ],
            [
                'task_name' => 'Team meeting',
                'description' => 'Discuss project timeline and deadlines with clients'
            ],
            [
                'task_name' => 'Go to gym',
                'description' => 'Workout session focusing on cardio and strength training'
            ],
            [
                'task_name' => 'Read a book',
                'description' => 'Continue reading Clean Code by Robert Martin'
            ],
            [
                'task_name' => 'Fix car',
                'description' => 'Take car to mechanic for oil change and maintenance'
            ],
            [
                'task_name' => 'Learn Python',
                'description' => 'Start Python programming course and practice coding'
            ],
            [
                'task_name' => 'Random task',
                'description' => 'This task has no specific keywords'
            ]
        ];
        
        $results = [];
        foreach ($testTasks as $task) {
            $category = $this->autoCategorizeTask($task);
            $results[] = [
                'task' => $task,
                'category' => $category
            ];
        }
        
        return response()->json([
            'message' => 'Task categorization test results',
            'results' => $results
        ]);
    }
    
    /**
     * Get all available categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories()
    {
        return response()->json([
            'categories' => ['Work', 'Personal', 'Learning', 'Other'],
            'message' => 'Available task categories'
        ]);
    }
}
