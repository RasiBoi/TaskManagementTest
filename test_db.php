<?php
// Enhanced database check script

echo "Testing Laravel Database Connection...\n";

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    // Get the database connection
    $db = $app->make('db');
    $pdo = $db->connection()->getPdo();
    echo "✅ Database connection successful!\n";
    
    // Test if tasks table exists
    $tables = $db->select("SELECT name FROM sqlite_master WHERE type='table' AND name='tasks'");
    if (count($tables) > 0) {
        echo "✅ Tasks table exists!\n";
        
        // Get table structure
        $columns = $db->select("PRAGMA table_info(tasks)");
        echo "\nTable structure:\n";
        foreach ($columns as $column) {
            echo "- {$column->name} ({$column->type})" . 
                 ($column->dflt_value ? " DEFAULT {$column->dflt_value}" : "") . "\n";
        }
        
        // Count tasks and check category data
        $count = $db->select("SELECT COUNT(*) as count FROM tasks")[0];
        echo "\n📊 Tasks count: " . $count->count . "\n";
        
        // Check last few tasks
        $tasks = $db->select("SELECT id, task_name, category FROM tasks ORDER BY id DESC LIMIT 3");
        echo "\nLast 3 tasks:\n";
        foreach ($tasks as $task) {
            echo "ID: {$task->id}, Name: {$task->task_name}, Category: {$task->category}\n";
        }
        
    } else {
        echo "❌ Tasks table does not exist!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
