<?php

namespace App\Services;

class OpenAICategorizationService
{
    private $apiKey;
    private $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Categorize a task using OpenAI
     *
     * @param array $task
     * @return string
     */
    public function categorizeTask($task)
    {
        $taskName = isset($task['task_name']) ? $task['task_name'] : '';
        $description = isset($task['description']) ? $task['description'] : '';

        // Create the prompt for OpenAI
        $prompt = $this->createPrompt($taskName, $description);

        try {
            $response = $this->callOpenAI($prompt);
            $category = $this->parseResponse($response);
            
            // Validate the category is one of our allowed categories
            return $this->validateCategory($category);
        } catch (Exception $e) {
            // Fallback to keyword-based categorization if OpenAI fails
            return $this->fallbackCategorization($task);
        }
    }

    /**
     * Create a well-structured prompt for OpenAI
     */
    private function createPrompt($taskName, $description)
    {
        return "You are a task categorization expert. Please categorize the following task into exactly ONE of these categories: Work, Personal, Learning, or Other.

Task Name: \"$taskName\"
Description: \"$description\"

Guidelines:
- Work: Professional tasks, meetings, projects, development, business activities
- Personal: Home activities, health, family, hobbies, personal errands
- Learning: Education, studying, courses, skill development, reading for knowledge
- Other: Tasks that don't clearly fit the above categories

Respond with ONLY the category name (Work, Personal, Learning, or Other). No explanation needed.

Category:";
    }

    /**
     * Make the API call to OpenAI
     */
    private function callOpenAI($prompt)
    {
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
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        // Handle SSL issues on Windows
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('OpenAI API request failed with code: ' . $httpCode);
        }

        return json_decode($response, true);
    }

    /**
     * Parse the OpenAI response to extract the category
     */
    private function parseResponse($response)
    {
        if (!isset($response['choices'][0]['message']['content'])) {
            throw new Exception('Invalid OpenAI response format');
        }

        $content = trim($response['choices'][0]['message']['content']);
        
        // Clean up the response - sometimes it might have extra text
        $content = preg_replace('/[^a-zA-Z]/', '', $content);
        
        return $content;
    }

    /**
     * Validate and normalize the category
     */
    private function validateCategory($category)
    {
        $validCategories = ['Work', 'Personal', 'Learning', 'Other'];
        
        // Case-insensitive search
        foreach ($validCategories as $valid) {
            if (strcasecmp($category, $valid) === 0) {
                return $valid;
            }
        }
        
        // If not found, return Other as default
        return 'Other';
    }

    /**
     * Fallback categorization using keywords (if OpenAI fails)
     */
    private function fallbackCategorization($task)
    {
        $taskName = strtolower(isset($task['task_name']) ? $task['task_name'] : '');
        $description = strtolower(isset($task['description']) ? $task['description'] : '');
        $combinedText = $taskName . ' ' . $description;

        $categories = [
            'Work' => ['project', 'meeting', 'presentation', 'client', 'development', 'api', 'programming', 'laravel', 'php'],
            'Personal' => ['family', 'gym', 'shopping', 'grocery', 'health', 'exercise', 'home', 'car', 'pet'],
            'Learning' => ['learn', 'study', 'course', 'tutorial', 'book', 'reading', 'education', 'skill']
        ];

        $maxScore = 0;
        $bestCategory = 'Other';

        foreach ($categories as $category => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (strpos($combinedText, $keyword) !== false) {
                    $score++;
                }
            }
            
            if ($score > $maxScore) {
                $maxScore = $score;
                $bestCategory = $category;
            }
        }

        return $bestCategory;
    }
}
