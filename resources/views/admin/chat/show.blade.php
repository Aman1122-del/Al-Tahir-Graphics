@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Chat Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Chat with {{ $otherUser->name }}</h1>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-sm text-gray-500">{{ $otherUser->email }}</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.chat.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Back to List
                        </a>
                        <a href="{{ route('admin.chat.unified.index') }}" 
                           class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            Unified Chat
                        </a>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="bg-white border border-gray-200 rounded-lg mb-6">
                    <div class="h-96 overflow-y-auto p-4" id="chatMessages">
                        @foreach($messages as $message)
                            <div class="mb-4 {{ $message->sender_id == Auth::id() ? 'flex justify-end' : 'flex justify-start' }}">
                                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id == Auth::id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-xs font-medium opacity-75">
                                            {{ $message->sender->name ?? 'Unknown' }}
                                        </span>
                                        <span class="text-xs opacity-75">
                                            {{ $message->created_at->format('g:i A') }}
                                        </span>
                                    </div>
                                    <div class="text-sm">{{ $message->message }}</div>
                                    
                                    @if($message->file_name)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 {{ $message->sender_id == Auth::id() ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700' }} rounded text-xs">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                {{ $message->file_name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reply Form -->
                <form id="replyForm" class="bg-gray-50 p-4 rounded-lg">
                    @csrf
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <textarea name="message" id="messageInput" rows="3" 
                                      class="w-full rounded-md border-gray-300 shadow-sm" 
                                      placeholder="Type your reply..." required></textarea>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <input type="file" id="fileInput" name="file" accept="image/*,.pdf,.doc,.docx" 
                                   class="hidden">
                            <button type="button" onclick="document.getElementById('fileInput').click()" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Attach
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                Send
                            </button>
                        </div>
                    </div>
                    <div id="fileInfo" class="mt-2 text-sm text-gray-600 hidden"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-scroll to bottom
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Safe timestamp formatting
function formatTimestamp(timestamp) {
    try {
        const date = new Date(timestamp);
        if (isNaN(date.getTime())) {
            return 'Invalid date';
        }
        return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    } catch (error) {
        console.error('Error formatting timestamp:', error);
        return 'Invalid date';
    }
}

// File input change handler
document.getElementById('fileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileInfo = document.getElementById('fileInfo');
    
    if (file) {
        fileInfo.textContent = `Selected: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
        fileInfo.classList.remove('hidden');
    } else {
        fileInfo.classList.add('hidden');
    }
});

// Reply form submission
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const messageInput = document.getElementById('messageInput');

    if (!messageInput.value.trim()) {
        alert('Please enter a message');
        return;
    }

    fetch(`{{ route('admin.chat.reply', $otherUser->id) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response format');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Add message to chat
            addMessageToChat(data.message);
            messageInput.value = '';
            document.getElementById('fileInput').value = '';
            document.getElementById('fileInfo').classList.add('hidden');
        } else {
            alert('Error sending message: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message: ' + error.message);
    });
});

// Add message to chat display
function addMessageToChat(message) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'mb-4 flex justify-end';
    
    const now = new Date();
    const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    messageDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg bg-blue-500 text-white">
            <div class="flex items-center space-x-2 mb-1">
                <span class="text-xs font-medium opacity-75">You</span>
                <span class="text-xs opacity-75">${timeString}</span>
            </div>
            <div class="text-sm">${message.message}</div>
            ${message.file_name ? `
                <div class="mt-2">
                    <span class="inline-flex items-center px-2 py-1 bg-blue-600 text-white rounded text-xs">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        ${message.file_name}
                    </span>
                </div>
            ` : ''}
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
}

// Real-time message polling (15 seconds)
let pollingInterval;
let lastMessageId = {{ $messages->count() > 0 ? $messages->last()->id : 0 }};

// Load new messages from database
async function loadNewMessages() {
    try {
        const response = await fetch(`{{ route('admin.chat.messages', $otherUser->id) }}?since=${lastMessageId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response format');
        }
        const data = await response.json();
        if (data.success && data.messages && data.messages.length > 0) {
            // Add new messages to chat
            data.messages.forEach(message => {
                addReceivedMessageToChat(message);
                lastMessageId = message.id;
            });
            scrollToBottom();
        }
    } catch (error) {
        console.error('Error loading new messages:', error);
    }
}

// Add received message to chat display
function addReceivedMessageToChat(message) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'mb-4 flex justify-start';
    
    const timeString = formatTimestamp(message.created_at);
    
    messageDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg bg-gray-200 text-gray-800">
            <div class="flex items-center space-x-2 mb-1">
                <span class="text-xs font-medium opacity-75">${message.sender ? message.sender.name : 'Unknown'}</span>
                <span class="text-xs opacity-75">${timeString}</span>
            </div>
            <div class="text-sm">${message.message}</div>
            ${message.file_name ? `
                <div class="mt-2">
                    <span class="inline-flex items-center px-2 py-1 bg-gray-300 text-gray-700 rounded text-xs">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        ${message.file_name}
                    </span>
                </div>
            ` : ''}
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
}

// Start polling for new messages
function startPolling() {
    pollingInterval = setInterval(loadNewMessages, 15000); // Poll every 15 seconds
}

// Stop polling
function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

// Scroll to bottom on page load and start polling
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    startPolling();
});

// Stop polling when page is hidden, resume when visible
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopPolling();
    } else {
        startPolling();
    }
});
</script>
@endsection
