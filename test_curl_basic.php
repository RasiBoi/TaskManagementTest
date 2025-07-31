<?php

echo "Testing basic cURL connectivity...\n\n";

// Test 1: Simple HTTP request
echo "1. Testing HTTP (httpbin.org):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://httpbin.org/get");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Error: " . ($error ?: "None") . "\n";
echo "Response length: " . strlen($response) . "\n\n";

// Test 2: HTTPS request
echo "2. Testing HTTPS (httpbin.org):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://httpbin.org/get");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification temporarily
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Error: " . ($error ?: "None") . "\n";
echo "Response length: " . strlen($response) . "\n\n";

// Test 3: OpenAI API with more verbose error checking
echo "3. Testing OpenAI API connectivity:\n";

// Read API key
$envFile = '.env';
$envContent = file_get_contents($envFile);
if (preg_match('/OPENAI_API_KEY=(.+)/', $envContent, $matches)) {
    $apiKey = trim($matches[1]);
    echo "API key loaded (length: " . strlen($apiKey) . ")\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/models");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    echo "Error: " . ($error ?: "None") . "\n";
    echo "Response length: " . strlen($response) . "\n";
    echo "Connection info:\n";
    echo "- Connect time: " . $info['connect_time'] . "s\n";
    echo "- Total time: " . $info['total_time'] . "s\n";
    echo "- DNS lookup: " . $info['namelookup_time'] . "s\n";
    
    if ($response && $httpCode == 200) {
        echo "✅ OpenAI API connection successful!\n";
        $data = json_decode($response, true);
        if (isset($data['data'])) {
            echo "Available models: " . count($data['data']) . "\n";
        }
    } else {
        echo "❌ OpenAI API connection failed\n";
        echo "Response: " . substr($response, 0, 200) . "\n";
    }
} else {
    echo "❌ Could not find API key in .env file\n";
}
