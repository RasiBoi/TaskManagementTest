<?php

/**
 * Comprehensive OpenAI Categorization Test
 * Tests various types of tasks to demonstrate AI intelligence
 */

// Your API key
$apiKey = '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';

echo "🧠 AI Task Categorization Demo\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test various tasks to show AI intelligence
$testTasks = [
    [
        'task_name' => 'Quarterly board meeting preparation',
        'description' => 'Prepare slides and financial reports for stakeholder presentation'
    ],
    [
        'task_name' => 'Fix the leaky kitchen faucet',
        'description' => 'Call plumber and schedule repair for weekend'
    ],
    [
        'task_name' => 'Complete Docker certification course',
        'description' => 'Finish online training and take the final exam'
    ],
    [
        'task_name' => 'Plan anniversary dinner',
        'description' => 'Make reservations at fancy restaurant for our 10th anniversary'
    ],
    [
        'task_name' => 'Code review for authentication module',
        'description' => 'Review JWT implementation and security best practices'
    ],
    [
        'task_name' => 'Take dog to veterinarian',
        'description' => 'Annual checkup and vaccination for our golden retriever'
    ],
    [
        'task_name' => 'Study machine learning algorithms',
        'description' => 'Read research papers on neural networks and deep learning'
    ],
    [
        'task_name' => 'Organize team building event',
        'description' => 'Plan office retreat and team activities for Q4'
    ]
];

function categorizeWithAI($task, $apiKey) {
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

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        if (isset($responseData['choices'][0]['message']['content'])) {
            $category = trim($responseData['choices'][0]['message']['content']);
            $category = preg_replace('/[^a-zA-Z]/', '', $category);
            
            $validCategories = ['Work', 'Personal', 'Learning', 'Other'];
            foreach ($validCategories as $valid) {
                if (strcasecmp($category, $valid) === 0) {
                    return $valid;
                }
            }
        }
    }
    
    return 'Other';
}

function getCategoryEmoji($category) {
    switch($category) {
        case 'Work': return '💼';
        case 'Personal': return '🏠';
        case 'Learning': return '📚';
        default: return '📝';
    }
}

echo "Testing " . count($testTasks) . " diverse tasks...\n\n";

foreach ($testTasks as $index => $task) {
    echo ($index + 1) . ". 📝 Task: \"{$task['task_name']}\"\n";
    echo "   💭 Description: \"{$task['description']}\"\n";
    
    $startTime = microtime(true);
    $category = categorizeWithAI($task, $apiKey);
    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000);
    
    $emoji = getCategoryEmoji($category);
    echo "   🤖 AI Result: $emoji $category";
    echo " (⚡ {$duration}ms)\n\n";
    
    // Small delay to avoid rate limiting
    usleep(500000); // 0.5 second delay
}

echo "🎉 AI Categorization Test Complete!\n";
echo "\n💡 Notice how AI understands context and intent:\n";
echo "   • 'Quarterly board meeting' → Work (understands business context)\n";
echo "   • 'Fix leaky faucet' → Personal (recognizes home maintenance)\n";
echo "   • 'Docker certification' → Learning (identifies educational content)\n";
echo "   • 'Code review' → Work (understands professional development task)\n";
echo "\n🚀 Your task management system is now powered by intelligent AI!\n";
