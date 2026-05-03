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

    // Check if question is fitness-related
    if (!$groqAPI->isFitnessQuestion($userMessage)) {
        // Non-fitness question - return rejection message
        $response = [
            'response' => "Sorry! 🏋️ I can only answer **fitness-related questions**.\n\n" .
                         "I can help you with:\n" .
                         "• **Workouts & Exercise** - routines, techniques, strength training\n" .
                         "• **Nutrition & Diet** - meal plans, healthy foods, supplements\n" .
                         "• **Weight Management** - weight loss, muscle gain, BMI calculations\n" .
                         "• **Fitness Tips** - injury prevention, recovery, motivation\n\n" .
                         "Please ask something related to fitness! 💪",
            'isFitnessRelated' => false,
            'format' => 'points'
        ];
        echo json_encode($response);
        exit;
    }

    // Detect response format preference
    $responseFormat = $groqAPI->detectResponseFormat($userMessage);

    // Extract fitness keywords
    $foundKeywords = $groqAPI->extractFitnessKeywords($userMessage);

    // Get conversation history from session (optional - for multi-turn conversations)
    $conversationHistory = [];

    // Send message to Groq API (only if fitness-related)
    $response = $groqAPI->chat($userMessage, $conversationHistory);

    // Return response with metadata
    echo json_encode([
        'response' => $response,
        'isFitnessRelated' => true,
        'format' => $responseFormat,
        'detectedKeywords' => $foundKeywords
    ]);

} catch (Exception $e) {
    // Error handling
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'response' => 'Sorry, an error occurred: ' . $e->getMessage()
    ]);
}

?>
