<?php

require_once 'api.php';

header('Content-Type: application/json');

// Get user message
$userMessage = $_POST['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['response' => 'Please ask a fitness question']);
    exit;
}

try {
    // Initialize Groq API client
    $groqAPI = new GroqAPI(GROQ_API_KEY, GROQ_API_URL, GROQ_MODEL);

    // Get conversation history from session (optional - for multi-turn conversations)
    // For now, we'll keep it simple with single-turn responses
    $conversationHistory = [];

    // Send message to Groq API
    $response = $groqAPI->chat($userMessage, $conversationHistory);

    // Return response
    echo json_encode(['response' => $response]);

} catch (Exception $e) {
    // Error handling
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'response' => 'Sorry, an error occurred: ' . $e->getMessage()
    ]);
}

?>
