<?php

// Test script to debug the issue
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    echo "Testing Task model...\n";
    
    // Try to use the Task model
    $task = new \App\Task();
    echo "✅ Task model loaded successfully\n";
    
    // Try to get all tasks
    $tasks = \App\Task::all();
    echo "✅ Task::all() works, found " . count($tasks) . " tasks\n";
    
    // Test the auto-categorization method
    require_once __DIR__ . '/app/Http/Controllers/TaskController.php';
    echo "✅ TaskController loaded\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
