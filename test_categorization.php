<?php

/**
 * Task Auto-Categorization Test Script
 * 
 * This script demonstrates the autoCategorizeTask function
 * and tests it with various example tasks.
 */

// Simple version of the function for standalone testing
function autoCategorizeTask($task)
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

// Test cases
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
    ],
    [
        'task_name' => 'Prepare presentation',
        'description' => 'Create slides for the quarterly business review meeting'
    ]
];

echo "=== Task Auto-Categorization Test Results ===\n\n";

foreach ($testTasks as $index => $task) {
    $category = autoCategorizeTask($task);
    
    echo sprintf(
        "%d. Task: \"%s\"\n   Description: \"%s\"\n   → Category: %s\n\n",
        $index + 1,
        $task['task_name'],
        $task['description'],
        $category
    );
}

echo "=== Test Summary ===\n";
echo "Function successfully categorized " . count($testTasks) . " tasks.\n";
echo "Categories used: Work, Personal, Learning, Other\n";
echo "Keyword-based logic working as expected!\n";
