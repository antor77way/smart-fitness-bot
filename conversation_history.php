<?php

/**
 * Session Handler for Conversation History
 * Optional feature for maintaining conversation context
 * 
 * Usage: Uncomment session_start() in chat.php and use this handler
 */

class ConversationHistory {
    private $sessionKey = 'fitness_bot_history';
    private $maxMessages = 10; // Keep last 10 messages for context

    /**
     * Initialize session and get history
     */
    public function getHistory() {
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
        return $_SESSION[$this->sessionKey];
    }

    /**
     * Add message to history
     */
    public function addMessage($role, $content) {
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }

        // Add new message
        $_SESSION[$this->sessionKey][] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => time()
        ];

        // Keep only last N messages
        if (count($_SESSION[$this->sessionKey]) > $this->maxMessages) {
            $_SESSION[$this->sessionKey] = array_slice(
                $_SESSION[$this->sessionKey],
                -$this->maxMessages
            );
        }
    }

    /**
     * Clear history
     */
    public function clearHistory() {
        $_SESSION[$this->sessionKey] = [];
    }

    /**
     * Get formatted history for API (without timestamps)
     */
    public function getFormattedHistory() {
        $history = $this->getHistory();
        $formatted = [];

        foreach ($history as $msg) {
            $formatted[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }

        return $formatted;
    }

    /**
     * Get history count
     */
    public function getMessageCount() {
        return count($this->getHistory());
    }
}

?>
