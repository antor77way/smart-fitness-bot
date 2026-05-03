<?php

/**
 * Groq API Handler for Fitness Bot
 */

require_once 'config.php';

header('Content-Type: application/json');

class GroqAPI {
    private $apiKey;
    private $apiUrl;
    private $model;

    public function __construct($apiKey, $apiUrl, $model) {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->model = $model;
    }

    /**
     * Send request to Groq API
     */
    public function chat($userMessage, $conversationHistory = []) {
        // Build conversation messages
        $messages = [];

        // Add system prompt
        $messages[] = [
            'role' => 'system',
            'content' => $this->getSystemPrompt()
        ];

        // Add conversation history
        foreach ($conversationHistory as $msg) {
            $messages[] = $msg;
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        // Prepare request
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ];

        // Make API call
        $response = $this->makeRequest($payload);

        return $response;
    }

    /**
     * Make HTTP request to Groq API
     */
    private function makeRequest($payload) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            throw new Exception('API Error (' . $httpCode . '): ' . ($errorData['error']['message'] ?? 'Unknown error'));
        }

        $data = json_decode($response, true);

        if (!isset($data['choices'][0]['message']['content'])) {
            throw new Exception('Invalid API response format');
        }

        return $data['choices'][0]['message']['content'];
    }

    /**
     * System prompt for fitness bot
     */
    private function getSystemPrompt() {
        return <<<'PROMPT'
You are a professional fitness coach and nutritionist with expertise in:
- Workout programs and exercise techniques
- Nutrition and diet planning
- Weight management and BMI guidance
- Muscle building and strength training
- Cardio and endurance training
- Recovery and injury prevention
- Fitness motivation and lifestyle coaching

Your responses should be:
1. Evidence-based and scientifically accurate
2. Personalized based on user's situation
3. Safe and injury-preventing (always recommend consulting doctors for medical concerns)
4. Motivational but realistic
5. Clear and easy to follow

When users ask about BMI, provide calculations if they give weight and height.
Always encourage proper form, warming up, and consulting professionals when needed.
Keep responses concise but informative (150-300 words typically).
PROMPT;
    }
}

?>
