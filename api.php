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
     * System prompt for fitness bot with professional segmented response format
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

CRITICAL FORMATTING RULES - Always maintain proper segmentation:

IF USER ASKS FOR POINT-STYLE (words like "list", "points", "steps", "bullets"):
- Create bullet points with • for each point
- Add BLANK LINE between sections
- Format: **Section Name:**
  • Point 1
  • Point 2

IF USER ASKS FOR PARAGRAPH-STYLE (words like "explain", "describe", "tell me"):
- Write in paragraphs with proper spacing
- Start with **Section Name:**
- Add BLANK LINE after title before paragraph
- Add BLANK LINE between paragraphs

IF USER DOESN'T SPECIFY FORMAT (most professional):
- Use mixed format: Combine brief intro paragraph + bullet points
- Add BLANK LINE between each section
- Example:
  **Overview:**
  Brief explanation here.

  **Key Points:**
  • Point 1
  • Point 2

SPACING RULES:
- Always add blank line after section heading
- Always add blank line between different sections
- Always add blank line between paragraphs
- Use 2+ blank lines to separate major sections

When users ask about BMI, provide calculations if they give weight and height.
Always encourage proper form, warming up, and consulting professionals when needed.
Keep responses concise but informative (150-300 words typically).
PROMPT;
    }

    /**
     * Detect response format preference from user message
     */
    public function detectResponseFormat($userMessage) {
        $lowerMessage = strtolower($userMessage);
        
        // Keywords for point-based responses
        $pointKeywords = ['list', 'points', 'steps', 'bullet', 'bullets', 'quickly', 'brief', 'summary', 'enumerate'];
        
        // Keywords for paragraph-based responses
        $paragraphKeywords = ['explain', 'describe', 'tell me', 'how does', 'understand', 'detailed', 'elaborate', 'in detail'];
        
        // Check for paragraph preference
        foreach ($paragraphKeywords as $keyword) {
            if (stripos($lowerMessage, $keyword) !== false) {
                return 'paragraph';
            }
        }
        
        // Check for point preference
        foreach ($pointKeywords as $keyword) {
            if (stripos($lowerMessage, $keyword) !== false) {
                return 'points';
            }
        }
        
        // Default to mixed (professional)
        return 'mixed';
    }

    /**
     * Calculate similarity between two strings (0-100)
     * Uses similar_text to handle spelling mistakes
     */
    private function calculateSimilarity($str1, $str2) {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        
        if ($len1 === 0 || $len2 === 0) {
            return 0;
        }
        
        similar_text($str1, $str2, $percentage);
        return $percentage;
    }

    /**
     * Check if question is fitness-related using fuzzy keyword matching
     * Handles spelling mistakes by allowing ~70% similarity
     */
    public function isFitnessQuestion($userMessage) {
        // Comprehensive list of fitness-related keywords
        $fitnessKeywords = [
            // Exercise & Workout
            'workout', 'exercise', 'training', 'gym', 'strength', 'cardio', 'running', 'cycling',
            'weights', 'lifting', 'muscle', 'push-up', 'squat', 'deadlift', 'bench press',
            'plank', 'dumbbell', 'barbell', 'stretching', 'yoga', 'pilates', 'zumba',
            'hiit', 'boxing', 'swimming', 'jogging', 'walking', 'sprinting',
            
            // Diet & Nutrition
            'diet', 'nutrition', 'food', 'meal', 'protein', 'carbs', 'fat', 'calorie',
            'eating', 'weight loss', 'weight gain', 'recipe', 'supplement', 'vitamin',
            'healthy eating', 'meal plan', 'macro', 'protein shake', 'smoothie',
            
            // Body & Health
            'body', 'muscle gain', 'fat loss', 'bmi', 'weight', 'height', 'slim', 'bulk',
            'tone', 'abs', 'bicep', 'tricep', 'chest', 'back', 'legs', 'shoulders',
            'flexibility', 'endurance', 'stamina', 'recovery', 'rest day',
            
            // Fitness Goals & General
            'fitness', 'health', 'physique', 'body shape', 'lose weight', 'gain muscle',
            'get fit', 'exercise routine', 'workout plan', 'fitness goal', 'beginner workout',
            'advanced workout', 'form', 'technique', 'injury prevention', 'warm-up',
            'cool-down', 'rep', 'set', 'reps', 'sets', 'frequency', 'duration'
        ];

        $lowerMessage = strtolower($userMessage);
        
        // Split message into words for fuzzy matching
        $words = preg_split('/\s+|[-,.]/', $lowerMessage, -1, PREG_SPLIT_NO_EMPTY);
        
        // First try exact substring match (fastest)
        foreach ($fitnessKeywords as $keyword) {
            if (stripos($lowerMessage, $keyword) !== false) {
                return true;
            }
        }
        
        // Then try fuzzy matching on individual words for spelling tolerance
        $similarity_threshold = 70; // 70% similarity = allow spelling mistakes
        
        foreach ($words as $word) {
            // Skip very short words
            if (strlen($word) < 3) continue;
            
            foreach ($fitnessKeywords as $keyword) {
                $similarity = $this->calculateSimilarity($word, $keyword);
                
                if ($similarity >= $similarity_threshold) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Get fitness-related keywords found in the message
     * Uses fuzzy matching to handle spelling mistakes
     */
    public function extractFitnessKeywords($userMessage) {
        $fitnessKeywords = [
            'workout', 'exercise', 'training', 'gym', 'strength', 'cardio', 'running', 'cycling',
            'weights', 'lifting', 'muscle', 'push-up', 'squat', 'deadlift', 'bench press',
            'plank', 'dumbbell', 'barbell', 'stretching', 'yoga', 'pilates', 'zumba',
            'hiit', 'boxing', 'swimming', 'jogging', 'walking', 'sprinting',
            'diet', 'nutrition', 'food', 'meal', 'protein', 'carbs', 'fat', 'calorie',
            'eating', 'weight loss', 'weight gain', 'recipe', 'supplement', 'vitamin',
            'healthy eating', 'meal plan', 'macro', 'protein shake', 'smoothie',
            'body', 'muscle gain', 'fat loss', 'bmi', 'weight', 'height', 'slim', 'bulk',
            'tone', 'abs', 'bicep', 'tricep', 'chest', 'back', 'legs', 'shoulders',
            'flexibility', 'endurance', 'stamina', 'recovery', 'rest day',
            'fitness', 'health', 'physique', 'body shape', 'lose weight', 'gain muscle'
        ];

        $foundKeywords = [];
        $lowerMessage = strtolower($userMessage);
        $similarity_threshold = 70;

        // Split message into words
        $words = preg_split('/\s+|[-,.]/', $lowerMessage, -1, PREG_SPLIT_NO_EMPTY);

        // Extract exact matches first
        foreach ($fitnessKeywords as $keyword) {
            if (stripos($lowerMessage, $keyword) !== false) {
                $foundKeywords[$keyword] = 100;
            }
        }

        // Then extract fuzzy matches
        foreach ($words as $word) {
            if (strlen($word) < 3) continue;

            foreach ($fitnessKeywords as $keyword) {
                // Skip if already found as exact match
                if (isset($foundKeywords[$keyword])) continue;

                $similarity = $this->calculateSimilarity($word, $keyword);
                
                if ($similarity >= $similarity_threshold) {
                    $foundKeywords[$keyword] = round($similarity, 1);
                }
            }
        }

        return array_keys($foundKeywords);
    }
}

?>
