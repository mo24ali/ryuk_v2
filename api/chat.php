<?php
/**
 * Mini AI Chat API
 * Using native PHP cURL to avoid dependency version conflicts (PHP 8.0 support)
 */

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

// --- Simple .env Loader ---
function loadEnv($path)
{
    if (!file_exists($path))
        return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        // Remove quotes if present
        $value = trim($value, '"\'');
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}

loadEnv(__DIR__ . '/../.env');

$apiKey = $_ENV['HF_TOKEN'] ?? getenv('HF_TOKEN');
$model = $_ENV['HF_MODEL'] ?? getenv('HF_MODEL') ?: 'mistralai/Mistral-7B-Instruct-v0.3';

if (!$apiKey) {
    echo json_encode(['error' => 'Hugging Face Token not configured in .env file.']);
    exit;
}

// --- Get Input ---
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
$history = $input['history'] ?? [];

if (empty($message)) {
    echo json_encode(['error' => 'Empty message.']);
    exit;
}

// --- Prepare Messages ---
$messages = [
    ['role' => 'system', 'content' => 'You are a helpful and friendly AI assistant.']
];

foreach (array_slice($history, -10) as $msg) {
    $messages[] = $msg;
}
$messages[] = ['role' => 'user', 'content' => $message];

// --- Hugging Face API Call (cURL - OpenAI Compatible) ---
$url = "https://router.huggingface.co/v1/chat/completions";

$data = [
    'model' => $model,
    'messages' => $messages,
    'temperature' => 0.7,
    'max_tokens' => 500
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

// Handle SSL issues if common in local XAMPP
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL Error: ' . $error]);
    exit;
}

$responseData = json_decode($response, true);

if ($httpCode !== 200) {
    http_response_code($httpCode);
    $errorMessage = $responseData['error'] ?? 'Hugging Face API returned an error.';
    if (is_array($errorMessage)) {
        $errorMessage = json_encode($errorMessage);
    }
    echo json_encode(['error' => 'Hugging Face API Error: ' . $errorMessage]);
    exit;
}

$reply = $responseData['choices'][0]['message']['content'];

echo json_encode([
    'reply' => $reply,
    'history' => array_merge($history, [
        ['role' => 'user', 'content' => $message],
        ['role' => 'assistant', 'content' => $reply]
    ])
]);
