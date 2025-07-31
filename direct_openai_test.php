<?php

/**
 * Direct OpenAI API Test
 * Tests OpenAI connection directly without Laravel dependencies
 */

// Your API key
$apiKey = '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';

echo "🔑 Testing OpenAI API Connection...\n";
echo "API Key: " . substr($apiKey, 0, 20) . "...\n\n";

// Test task
$task = [
    'task_name' => 'Develop REST API endpoints',
    'description' => 'Build authentication and CRUD operations for the web application'
];

echo "📝 Testing Task:\n";
echo "Name: {$task['task_name']}\n";
echo "Description: {$task['description']}\n\n";

// Create prompt
$prompt = "You are a task categorization expert. Please categorize the following task into exactly ONE of these categories: Work, Personal, Learning, or Other.

Task Name: \"{$task['task_name']}\"
Description: \"{$task['description']}\"

Guidelines:
- Work: Professional tasks, meetings, projects, development, business activities
- Personal: Home activities, health, family, hobbies, personal errands
- Learning: Education, studying, courses, skill development, reading for knowledge
- Other: Tasks that don't clearly fit the above categories

Respond with ONLY the category name (Work, Personal, Learning, or Other). No explanation needed.

Category:";

// API request data
$data = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        [
            'role' => 'user',
            'content' => $prompt
        ]
    ],
    'max_tokens' => 10,
    'temperature' => 0.3
];

echo "🌐 Making API request to OpenAI...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
// Disable SSL verification for Windows
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error\n";
    exit;
}

echo "📡 HTTP Response Code: $httpCode\n";

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    
    if (isset($responseData['choices'][0]['message']['content'])) {
        $category = trim($responseData['choices'][0]['message']['content']);
        echo "✅ OpenAI Response: '$category'\n";
        
        // Clean up category
        $category = preg_replace('/[^a-zA-Z]/', '', $category);
        
        // Validate category
        $validCategories = ['Work', 'Personal', 'Learning', 'Other'];
        $finalCategory = 'Other';
        
        foreach ($validCategories as $valid) {
            if (strcasecmp($category, $valid) === 0) {
                $finalCategory = $valid;
                break;
            }
        }
        
        echo "🎯 Final Category: $finalCategory\n";
        
        // Get emoji for category
        $emoji = '';
        switch($finalCategory) {
            case 'Work': $emoji = '💼'; break;
            case 'Personal': $emoji = '🏠'; break;
            case 'Learning': $emoji = '📚'; break;
            default: $emoji = '📝'; break;
        }
        
        echo "🎉 SUCCESS! Task categorized as: $emoji $finalCategory\n";
        
    } else {
        echo "❌ Unexpected response format\n";
        echo "Response: $response\n";
    }
} else {
    echo "❌ API Error (HTTP $httpCode)\n";
    echo "Response: $response\n";
    
    $errorData = json_decode($response, true);
    if (isset($errorData['error']['message'])) {
        echo "Error message: " . $errorData['error']['message'] . "\n";
    }
}

echo "\n🧪 Test completed!\n";
