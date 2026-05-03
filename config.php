<?php

/**
 * Configuration file for Fitness Bot API
 */

// Load environment variables from .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Groq API Configuration
define('GROQ_API_KEY', $_ENV['GROQ_API_KEY'] ?? getenv('GROQ_API_KEY') ?? '');
define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');
define('GROQ_MODEL', 'llama-3.1-8b-instant'); // Fast, reliable model

// Validate API Key
if (empty(GROQ_API_KEY)) {
    die(json_encode([
        'error' => 'Groq API key not configured. Please set GROQ_API_KEY in .env file'
    ]));
}

?>
