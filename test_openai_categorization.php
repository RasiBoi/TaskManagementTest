<?php

/**
 * OpenAI Categorization Test Script
 * 
 * This script tests the OpenAI integration for task categorization
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app to get environment variables
$app = require_once __DIR__ . '/bootstrap/app.php';

// Test the OpenAI service
try {
    require_once __DIR__ . '/app/Services/OpenAICategorizationService.php';
    
    $apiKey = env('OPENAI_API_KEY');
    
    if (!isValidOpenAIKey($apiKey)) {
        echo "❌ OpenAI API key not configured or invalid!\n";
        echo "Please add your OpenAI API key to the .env file:\n";
        echo "OPENAI_API_KEY=sk-your-actual-key-here\n\n";
        echo "🔄 Falling back to keyword-based categorization...\n\n";
        
        // Test fallback categorization
        testFallbackCategorization();
        exit;
    }
    
    echo "✅ OpenAI API key found: " . substr($apiKey, 0, 20) . "...\n";
    echo "🧠 Testing OpenAI categorization...\n\n";
    
    $service = new App\Services\OpenAICategorizationService($apiKey);
    
    // Test tasks
    $testTasks = [
        [
            'task_name' => 'Implement user authentication system',
            'description' => 'Build JWT authentication for the web application with login and registration'
        ],
        [
            'task_name' => 'Buy birthday gift for mom',
            'description' => 'Find a nice present for mother\'s birthday celebration next week'
        ],
        [
            'task_name' => 'Complete React.js course',
            'description' => 'Finish the advanced React tutorial on building modern web applications'
        ],
        [
            'task_name' => 'Plan weekend camping trip',
            'description' => 'Organize outdoor adventure with friends and prepare equipment'
        ],
        [
            'task_name' => 'Quarterly business review meeting',
            'description' => 'Present financial results and strategic planning to stakeholders'
        ]
    ];
    
    echo "🔍 Testing " . count($testTasks) . " tasks with OpenAI:\n";
    echo str_repeat("=", 60) . "\n\n";
    
    foreach ($testTasks as $index => $task) {
        echo ($index + 1) . ". Task: \"{$task['task_name']}\"\n";
        echo "   Description: \"{$task['description']}\"\n";
        
        try {
            $category = $service->categorizeTask($task);
            echo "   🤖 OpenAI Result: " . getCategoryEmoji($category) . " " . $category . "\n";
        } catch (Exception $e) {
            echo "   ❌ OpenAI Error: " . $e->getMessage() . "\n";
            echo "   🔄 Using fallback categorization...\n";
            // Add fallback logic here if needed
        }
        
        echo "\n";
    }
    
    echo "✅ OpenAI categorization test completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "🔄 Testing fallback categorization instead...\n\n";
    testFallbackCategorization();
}

function getCategoryEmoji($category) {
    switch ($category) {
        case 'Work': return '💼';
        case 'Personal': return '🏠';
        case 'Learning': return '📚';
        default: return '📝';
    }
}

function testFallbackCategorization() {
    echo "🔤 Testing keyword-based fallback categorization:\n";
    echo str_repeat("=", 50) . "\n\n";
    
    $testTasks = [
        ['task_name' => 'Team project meeting', 'description' => 'Discuss API development'],
        ['task_name' => 'Grocery shopping', 'description' => 'Buy milk and bread'],
        ['task_name' => 'Learn Python', 'description' => 'Study programming tutorial'],
        ['task_name' => 'Random task', 'description' => 'Something without keywords']
    ];
    
    foreach ($testTasks as $index => $task) {
        echo ($index + 1) . ". \"{$task['task_name']}\" → ";
        $category = keywordBasedCategorization($task);
        echo getCategoryEmoji($category) . " " . $category . "\n";
    }
}

function keywordBasedCategorization($task) {
    $text = strtolower($task['task_name'] . ' ' . $task['description']);
    
    if (strpos($text, 'project') !== false || strpos($text, 'meeting') !== false || strpos($text, 'api') !== false) {
        return 'Work';
    } elseif (strpos($text, 'shopping') !== false || strpos($text, 'grocery') !== false || strpos($text, 'buy') !== false) {
        return 'Personal';
    } elseif (strpos($text, 'learn') !== false || strpos($text, 'study') !== false || strpos($text, 'tutorial') !== false) {
        return 'Learning';
    }
    
    return 'Other';
}

function isValidOpenAIKey($key) {
    return !empty($key) && 
           $key !== 'your_openai_api_key_here' && 
           (strpos($key, 'sk-') === 0 || strpos($key, 'skproj') === 0);
}
