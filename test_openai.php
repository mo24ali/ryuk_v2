<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
echo "API Key length: " . strlen($apiKey) . "\n";

try {
    echo "Creating OpenAI client...\n";
    $client = OpenAI::client($apiKey);
    echo "Client created.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (Throwable $t) {
    echo "Fatal Error: " . $t->getMessage() . "\n";
}
