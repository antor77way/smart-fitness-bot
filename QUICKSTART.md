# 🚀 Quick Start Guide - Fitness Bot API Setup

## 5-Minute Setup

### Step 1: Get Your Groq API Key (2 minutes)

1. Go to https://console.groq.com
2. Click "Sign Up" (or "Log In" if you have an account)
3. Complete the registration
4. Once logged in, go to "API Keys" section
5. Click "Create API Key"
6. Copy the key (it looks like: `gsk_xxxxxxxxxxxxx...`)

### Step 2: Configure Your Project (2 minutes)

1. **Open your project folder:** `c:\xampp\htdocs\fitness_bot\`

2. **Find `.env.example` file** and create a copy named `.env`
   - Right-click `.env.example` → Copy
   - Right-click in empty space → Paste
   - Rename to `.env`

3. **Edit `.env` file** with a text editor:
   ```
   GROQ_API_KEY=gsk_xxxxxxxxxxxxx
   ```
   Replace `gsk_xxxxxxxxxxxxx` with your actual API key

4. **Save the file**

### Step 3: Start Your Project (1 minute)

#### If using XAMPP:
1. Open XAMPP Control Panel
2. Click "Start" on Apache
3. Open your browser and go to: `http://localhost/fitness_bot/`

#### If using PHP built-in server:
```bash
# Navigate to project folder
cd c:\xampp\htdocs\fitness_bot

# Run PHP server
php -S localhost:8000

# Then open: http://localhost:8000
```

### Step 4: Test It! (Optional)

Visit this URL to test your setup:
```
http://localhost/fitness_bot/test.php
```

You should see:
- ✅ API Key configured
- ✅ cURL extension enabled
- ✅ API Connection Successful

## You're Ready! 🎉

Start chatting with your AI fitness coach by asking questions like:

- "How do I build muscle?"
- "What's my BMI if I weigh 75kg and am 175cm tall?"
- "Create a beginner workout plan"
- "What should I eat to lose weight?"
- "How can I recover faster?"

---

## Troubleshooting Quick Links

| Problem | Solution |
|---------|----------|
| "API key not configured" | Make sure `.env` file exists with your key |
| "cURL Error" | Check if PHP cURL is enabled in XAMPP |
| "Invalid API response" | Your API key might be wrong - get a new one |
| "Timeout" | Groq API is slow - try again in a moment |

## Need More Help?

- 📖 Read the full [README.md](README.md)
- 🧪 Run [test.php](test.php) to diagnose issues
- 🔗 Check [Groq Docs](https://console.groq.com/docs)

---

**Your Fitness Bot is now powered by AI!** 💪🤖
