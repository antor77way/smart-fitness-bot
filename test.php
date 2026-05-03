<?php

/**
 * Test endpoint to verify Groq API connection
 * Visit: http://localhost/fitness_bot/test.php
 */

require_once 'api.php';

header('Content-Type: application/json');

?>

<!DOCTYPE html>
<html>
<head>
    <title>Groq API Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>

<h1>🏋️ Fitness Bot - Groq API Test</h1>

<?php

echo '<h2>Configuration Check:</h2>';

// Check API Key
if (empty(GROQ_API_KEY)) {
    echo '<p class="error">❌ API Key not configured</p>';
    echo '<p>Please add GROQ_API_KEY to .env file</p>';
} else {
    echo '<p class="success">✅ API Key configured</p>';
    echo '<p><strong>API URL:</strong> ' . GROQ_API_URL . '</p>';
    echo '<p><strong>Model:</strong> ' . GROQ_MODEL . '</p>';
}

// Check PHP Extensions
echo '<h2>System Check:</h2>';

if (extension_loaded('curl')) {
    echo '<p class="success">✅ cURL extension enabled</p>';
} else {
    echo '<p class="error">❌ cURL extension NOT enabled</p>';
}

if (function_exists('json_encode')) {
    echo '<p class="success">✅ JSON support available</p>';
} else {
    echo '<p class="error">❌ JSON support NOT available</p>';
}

// Test API Call
echo '<h2>API Connection Test:</h2>';

if (!empty(GROQ_API_KEY)) {
    try {
        $groqAPI = new GroqAPI(GROQ_API_KEY, GROQ_API_URL, GROQ_MODEL);
        $testMessage = "Say 'Hello! I am your fitness coach.'";
        $response = $groqAPI->chat($testMessage, []);
        
        echo '<p class="success">✅ API Connection Successful!</p>';
        echo '<h3>Test Response:</h3>';
        echo '<pre>' . htmlspecialchars($response) . '</pre>';
        
    } catch (Exception $e) {
        echo '<p class="error">❌ API Connection Failed</p>';
        echo '<p class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<h3>Troubleshooting Tips:</h3>';
        echo '<ul>';
        echo '<li>Check if your Groq API key is correct</li>';
        echo '<li>Verify your API key is active at https://console.groq.com</li>';
        echo '<li>Check your internet connection</li>';
        echo '<li>Try again in a few moments</li>';
        echo '</ul>';
    }
} else {
    echo '<p class="error">❌ Skipping API test - API key not configured</p>';
}

?>

<h2>Next Steps:</h2>
<ol>
    <li>If all checks pass ✅, visit <a href="index.html">index.html</a> to use the chatbot</li>
    <li>If API test failed ❌, see troubleshooting in README.md</li>
</ol>

<hr>
<p style="color: gray; font-size: 12px;">
    Test page last updated: <?php echo date('Y-m-d H:i:s'); ?>
</p>

</body>
</html>
