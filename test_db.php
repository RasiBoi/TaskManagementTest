<?php
// Simple test to check database connectivity

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
        
        // Count tasks
        $count = $db->select("SELECT COUNT(*) as count FROM tasks")[0];
        echo "📊 Tasks count: " . $count->count . "\n";
    } else {
        echo "❌ Tasks table does not exist!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
