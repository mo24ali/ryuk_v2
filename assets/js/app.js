document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const typingIndicator = document.querySelector('.typing');

    let history = [];

    const appendMessage = (role, text) => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${role === 'user' ? 'user-message' : 'ai-message'}`;
        msgDiv.textContent = text;
        chatBox.appendChild(msgDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
    };

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = userInput.value.trim();
        if (!message) return;

        // Clear input and show user message
        userInput.value = '';
        appendMessage('user', message);

        // Show typing indicator
        typingIndicator.style.display = 'block';
        sendBtn.disabled = true;

        try {
            const response = await fetch('api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message, history })
            });

            const data = await response.json();

            typingIndicator.style.display = 'none';
            sendBtn.disabled = false;

            if (data.error) {
                appendMessage('ai', 'Error: ' + data.error);
                console.log("error1");
            } else {
                appendMessage('ai', data.reply);
                history = data.history;
                console.log("error2");

            }
        } catch (error) {
            typingIndicator.style.display = 'none';
            sendBtn.disabled = false;
            appendMessage('ai', 'Something went wrong. Please try again.');
            console.error(error);
        }
    });

    // Auto-focus input
    userInput.focus();
});
