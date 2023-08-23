$(document).ready(function() {
    // DOM elements
    const messageInput = $('#message-input');
    const sendButton = $('#send-button');
    const chatMessages = $('#chat-messages');

    // Fetch and display chat history
    $.get('/api/messages', function(messages) {
        messages.forEach(function(message) {
            chatMessages.append(`<div>${message.user.name}: ${message.message}</div>`);
        });
    });

    // Send message when send button is clicked
    sendButton.click(function() {
        const message = messageInput.val();
        if (message.trim() !== '') {
            $.post('/api/messages', { message: message }, function(response) {
                console.log(response);
                chatMessages.append(`<div>You: ${response.message}</div>`);
                messageInput.val('');
            });
        }
    });
});
