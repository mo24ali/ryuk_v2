<?php
/**
 * Test Hugging Face Inference API (OpenAI Compatible)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

function loadEnv($path)
{
    if (!file_exists($path))
        return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value, '"\'');
    }
}

loadEnv(__DIR__ . '/.env');

$apiKey = $_ENV['HF_TOKEN'] ?? null;
$model = $_ENV['HF_MODEL'] ?? 'mistralai/Mistral-7B-Instruct-v0.3';

if (!$apiKey) {
    die("Error: HF_TOKEN not found in .env\n");
}

echo "Testing Hugging Face API...\n";
echo "Model: $model\n";

$url = "https://router.huggingface.co/v1/chat/completions";
$data = [
    'model' => $model,
    'messages' => [
        ['role' => 'user', 'content' => 'Hello, who are you?']
    ],
    'max_tokens' => 100
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo "cURL Error: " . $error . "\n";
    echo "Retrying without SSL verification...\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($response === false) {
        die("Still failing: " . $error . "\n");
    }
}

echo "HTTP Code: $httpCode\n";
echo "Response: \n";
echo $response . "\n";

if ($httpCode === 200) {
    $result = json_decode($response, true);
    echo "\nAssistant Reply: " . $result['choices'][0]['message']['content'] . "\n";
} else {
    echo "\nFailed to get a valid response.\n";
}
