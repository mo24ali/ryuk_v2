<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo "<pre>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "Autoloaded successfully.\n";
    $client = OpenAI::client('dummy');
    echo "Client created.\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
echo "</pre>";
