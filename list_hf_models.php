<?php
/**
 * List available models from Hugging Face Router
 */

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

if (!$apiKey)
    die("Error: HF_TOKEN not found\n");

$url = "https://router.huggingface.co/v1/models";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (isset($data['data'])) {
    foreach ($data['data'] as $model) {
        echo $model['id'] . "\n";
    }
} else {
    echo "Error: " . $response . "\n";
}
