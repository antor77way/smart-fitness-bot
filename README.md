# Fitness Bot - API-Based Chatbot

A professional fitness coaching chatbot powered by **Groq API** (Previously rule-based, now converted to API integration).

## Overview

This project has been converted from a rule-based chatbot to an **API-based chatbot** using the Groq API. The chatbot provides fitness guidance, workout plans, diet advice, and personalized coaching through intelligent API calls.

### What Changed?

**Before:** Rule-based responses using keyword matching and local data files
**After:** AI-powered responses using Groq API with natural language understanding

## Features

✅ Natural language understanding through Groq API
✅ Professional fitness coaching and guidance
✅ BMI calculations and health metrics
✅ Personalized workout recommendations
✅ Diet and nutrition planning
✅ Recovery and injury prevention tips
✅ 24/7 availability
✅ Fast response times with Groq's optimized models

## Project Structure

```
fitness_bot/
├── chat.php              # Main API endpoint (calls Groq API)
├── api.php              # Groq API handler and configuration
├── config.php           # Configuration and environment variables
├── index.html           # Frontend UI
├── script.js            # Frontend logic
├── .env.example         # Example environment file
├── .gitignore           # Git ignore file
├── data.json            # (Optional) Can be used for additional data
├── fitness_knowledge.txt # (Optional) Legacy knowledge base
└── README.md            # This file
```

## Prerequisites

- PHP 7.4+ with cURL extension
- Groq API Key (free at https://console.groq.com)
- A web server (Apache, Nginx, etc.) or PHP built-in server

## Installation & Setup

### Step 1: Get Your Groq API Key

1. Visit https://console.groq.com
2. Sign up for a free account
3. Create a new API key
4. Copy your API key

### Step 2: Configure Environment Variables

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` and add your Groq API key:
   ```
   GROQ_API_KEY=your_groq_api_key_here
   ```

**Important:** Never commit `.env` to version control (it's in `.gitignore`)

### Step 3: Verify PHP cURL Extension

Make sure your PHP installation has cURL enabled. Create a test file:

```php
<?php
if (extension_loaded('curl')) {
    echo "cURL is enabled!";
} else {
    echo "cURL is NOT enabled!";
}
?>
```

If cURL is not enabled:
- **Windows (XAMPP):** Uncomment `extension=curl` in `php.ini`
- **Linux:** Install with `sudo apt-get install php-curl`
- **macOS:** Use Homebrew or XAMPP

### Step 4: Run the Application

#### Option A: Using XAMPP (Recommended)
1. Place the project in `c:\xampp\htdocs\fitness_bot\`
2. Start Apache in XAMPP Control Panel
3. Open `http://localhost/fitness_bot/`

#### Option B: Using PHP Built-in Server
```bash
cd fitness_bot
php -S localhost:8000
```
Then open `http://localhost:8000`

## How It Works

### Request Flow

```
User Input (Frontend)
    ↓
script.js sends POST to chat.php
    ↓
chat.php calls GroqAPI class
    ↓
api.php sends request to Groq API servers
    ↓
Groq API processes with fitness system prompt
    ↓
Response sent back to frontend
    ↓
Message displayed in chatbox
```

### API Request Structure

```php
POST /chat.php
Content-Type: application/x-www-form-urlencoded

message=How do I build muscle?
```

### API Response Structure

```json
{
  "response": "To build muscle effectively, you need to..."
}
```

## Configuration

### Available Models in Groq

The project uses **`llama-3.1-70b-versatile`** by default (latest, most reliable). You can change it in `config.php`:

```php
define('GROQ_MODEL', 'llama-3.1-70b-versatile');
```

Other currently supported models:
- `llama-3.1-8b-instant` - Lightweight & faster
- `gemma-2-9b-it` - Good balance of speed and quality
- `mixtral-8x7b-32768` - ⚠️ Deprecated (no longer supported)

### Customizing the System Prompt

Edit the `getSystemPrompt()` method in `api.php` to customize the bot's behavior:

```php
private function getSystemPrompt() {
    return <<<'PROMPT'
    Your custom system prompt here...
    PROMPT;
}
```

## Usage Examples

### Example 1: Workout Advice
```
User: How should I start working out as a beginner?
Bot: As a beginner, start with these fundamentals...
```

### Example 2: BMI Calculation
```
User: I weigh 75kg and my height is 175cm, what's my BMI?
Bot: Your BMI is 24.5, which falls in the normal weight range...
```

### Example 3: Diet Plan
```
User: What should I eat to lose weight?
Bot: For weight loss, focus on a calorie deficit with...
```

## Troubleshooting

### Issue: "API key not configured"
**Solution:** Make sure `.env` file exists and contains `GROQ_API_KEY=your_actual_key`

### Issue: "cURL Error"
**Solution:** Check if cURL extension is enabled in PHP. See "Verify PHP cURL Extension" above.

### Issue: API Error (401 Unauthorized)
**Solution:** Verify your Groq API key is correct and active. Get a new one from https://console.groq.com

### Issue: Timeout errors
**Solution:** Groq API might be slow. Try again or check Groq's status page.

### Issue: "Invalid API response format"
**Solution:** The API format might have changed. Check Groq API documentation at https://console.groq.com/docs

## Advanced Features

### Enable Conversation History (Optional)

To maintain conversation context across multiple messages, implement session-based storage:

```php
// In chat.php
session_start();

$conversationHistory = $_SESSION['history'] ?? [];
$response = $groqAPI->chat($userMessage, $conversationHistory);

// Store in session
$_SESSION['history'][] = ['role' => 'user', 'content' => $userMessage];
$_SESSION['history'][] = ['role' => 'assistant', 'content' => $response];
```

### Add Rate Limiting

Prevent abuse:

```php
// Check message frequency
if (!isset($_SESSION['last_message_time'])) {
    $_SESSION['last_message_time'] = time();
} else {
    if (time() - $_SESSION['last_message_time'] < 1) {
        die(json_encode(['error' => 'Please wait before sending another message']));
    }
    $_SESSION['last_message_time'] = time();
}
```

### Store Messages to Database

For production, store conversations:

```php
// Add to database
$mysqli->query("INSERT INTO messages VALUES (NULL, '$user_id', '$userMessage', '$response', NOW())");
```

## Security Considerations

⚠️ **Important Security Notes:**

1. **Never expose API key in frontend** - Keep it server-side only
2. **Use HTTPS in production** - Encrypt API key transmission
3. **Validate user input** - Prevent injection attacks
4. **Add authentication** - Implement user login for production
5. **Rate limit requests** - Prevent API abuse
6. **Monitor API costs** - Set Groq API usage limits in console

### Production Checklist

- [ ] Move `.env` outside web root
- [ ] Enable HTTPS
- [ ] Add user authentication
- [ ] Implement database for message storage
- [ ] Add request rate limiting
- [ ] Set up error logging
- [ ] Configure CORS properly
- [ ] Add input validation
- [ ] Monitor API costs
- [ ] Set up backups

## Cost Estimation

**Groq API Pricing (Free Tier Available):**
- Free tier includes generous API limits
- Ideal for development and low-traffic applications
- Check https://console.groq.com/pricing for current rates

## Support & Resources

- 📖 [Groq API Documentation](https://console.groq.com/docs)
- 🚀 [Groq Console](https://console.groq.com)
- 💬 [Community Support](https://community.groq.com)

## File Descriptions

| File | Purpose |
|------|---------|
| `chat.php` | Main endpoint that receives messages and calls Groq API |
| `api.php` | Groq API wrapper class handling API communication |
| `config.php` | Configuration management and environment variables |
| `index.html` | Frontend UI with chat interface |
| `script.js` | Frontend logic for sending/receiving messages |
| `.env` | API key and sensitive configuration (not in repo) |
| `data.json` | Optional local data (legacy, not actively used) |

## Next Steps

1. ✅ Set up `.env` with your Groq API key
2. ✅ Test the chatbot with various fitness questions
3. ✅ Customize the system prompt for your needs
4. ✅ Deploy to production with proper security measures
5. ✅ Monitor API usage and costs

## License

This project is open source and available for personal and commercial use.

---

**Happy Coaching! 💪**

For questions or issues, feel free to check the troubleshooting section or consult the Groq documentation.
