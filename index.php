<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini AI Chatbot</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="chat-container">
        <div class="chat-header">
            <div class="status-dot"></div>
            <h1>Mini AI Assistant</h1>
        </div>

        <div id="chat-box">
            <div class="message ai-message">
                Hello! I'm your AI assistant. How can I help you today?
            </div>
        </div>

        <div class="input-area">
            <form id="chat-form">
                <div class="input-container">
                    <input type="text" id="user-input" placeholder="Type your message here..." autocomplete="off">
                    <button type="submit" id="send-btn">
                        <span>Send</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </div>
                <div class="typing">AI is thinking...</div>
            </form>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>

</html>